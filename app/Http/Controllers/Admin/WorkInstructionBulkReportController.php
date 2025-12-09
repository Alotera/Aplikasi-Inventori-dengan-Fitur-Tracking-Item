<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkInstruction;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Carbon\Carbon;

class WorkInstructionBulkReportController extends Controller
{
    public function index(): View
    {
        // Get filter options
        $users = User::where('role', 'user')->where('is_active', true)->orderBy('name')->get();
        
        return view('admin.work-instructions.bulk-report.index', compact('users'));
    }

    public function generatePdf(Request $request)
    {
        $validated = $request->validate([
            'date_from' => 'nullable|date',
            'date_to' => 'nullable|date|after_or_equal:date_from',
            'type' => 'nullable|in:checking,ambil,all',
            'status' => 'nullable|in:pending,in_progress,completed,overdue,all',
            'user_id' => 'nullable|exists:users,id',
            'progress_min' => 'nullable|integer|min:0|max:100',
            'progress_max' => 'nullable|integer|min:0|max:100|gte:progress_min',
        ], [
            'date_to.after_or_equal' => 'Tanggal akhir harus lebih besar atau sama dengan tanggal awal.',
            'progress_max.gte' => 'Progress maksimal harus lebih besar atau sama dengan progress minimal.',
        ]);

        // Build query
        $query = WorkInstruction::with(['assignedUser', 'items', 'statusProgress'])
            ->orderBy('created_at', 'desc');

        // Apply filters
        if (!empty($validated['date_from'])) {
            $query->whereDate('created_at', '>=', $validated['date_from']);
        }

        if (!empty($validated['date_to'])) {
            $query->whereDate('created_at', '<=', $validated['date_to']);
        }

        if (!empty($validated['type']) && $validated['type'] !== 'all') {
            $query->where('type', $validated['type']);
        }

        if (!empty($validated['status']) && $validated['status'] !== 'all') {
            $query->where('status', $validated['status']);
        }

        if (!empty($validated['user_id'])) {
            $query->where('assigned_user_id', $validated['user_id']);
        }

        $workInstructions = $query->get();

        // Update status for all work instructions
        foreach ($workInstructions as $wi) {
            $wi->updateStatus();
        }

        // Apply progress filters after status update
        if (!empty($validated['progress_min']) || !empty($validated['progress_max'])) {
            $workInstructions = $workInstructions->filter(function ($wi) use ($validated) {
                $progress = $wi->calculateProgressPercentage();
                
                if (!empty($validated['progress_min']) && $progress < $validated['progress_min']) {
                    return false;
                }
                
                if (!empty($validated['progress_max']) && $progress > $validated['progress_max']) {
                    return false;
                }
                
                return true;
            });
        }

        // Generate statistics
        $stats = [
            'total' => $workInstructions->count(),
            'checking' => $workInstructions->where('type', 'checking')->count(),
            'ambil' => $workInstructions->where('type', 'ambil')->count(),
            'completed' => $workInstructions->where('status', 'completed')->count(),
            'in_progress' => $workInstructions->where('status', 'in_progress')->count(),
            'pending' => $workInstructions->where('status', 'pending')->count(),
            'overdue' => $workInstructions->where('status', 'overdue')->count(),
        ];

        // Calculate average progress
        $totalProgress = $workInstructions->sum(function ($wi) {
            return $wi->calculateProgressPercentage();
        });
        $stats['average_progress'] = $workInstructions->count() > 0 
            ? round($totalProgress / $workInstructions->count(), 2) 
            : 0;

        // Generate filename
        $dateRange = '';
        if (!empty($validated['date_from']) && !empty($validated['date_to'])) {
            $dateRange = '_' . Carbon::parse($validated['date_from'])->format('Y-m-d') . '_to_' . Carbon::parse($validated['date_to'])->format('Y-m-d');
        } elseif (!empty($validated['date_from'])) {
            $dateRange = '_from_' . Carbon::parse($validated['date_from'])->format('Y-m-d');
        } elseif (!empty($validated['date_to'])) {
            $dateRange = '_until_' . Carbon::parse($validated['date_to'])->format('Y-m-d');
        }

        $filename = 'WI_Bulk_Report' . $dateRange . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';

        // Generate PDF
        $pdf = Pdf::loadView('admin.work-instructions.bulk-report.pdf-template', [
            'workInstructions' => $workInstructions,
            'stats' => $stats,
            'filters' => $validated,
            'generatedAt' => now(),
        ]);

        // Set paper size and orientation
        $pdf->setPaper('A4', 'landscape');

        // Return PDF download
        return $pdf->download($filename);
    }
}
