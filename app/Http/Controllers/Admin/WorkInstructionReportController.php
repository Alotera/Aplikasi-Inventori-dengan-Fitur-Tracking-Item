<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkInstruction;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;

class WorkInstructionReportController extends Controller
{
    public function generatePdf(WorkInstruction $workInstruction)
    {
        // Load relationships
        $workInstruction->load(['assignedUser', 'items', 'statusProgress']);
        
        // Update status
        $workInstruction->updateStatus();
        
        // Generate filename
        $filename = 'WI_Report_' . $workInstruction->wi_number . '_' . now()->format('Y-m-d_H-i-s') . '.pdf';
        
        // Generate PDF based on type
        if ($workInstruction->type === 'checking') {
            $pdf = Pdf::loadView('admin.work-instructions.reports.checking-pdf', compact('workInstruction'));
        } else {
            $pdf = Pdf::loadView('admin.work-instructions.reports.ambil-pdf', compact('workInstruction'));
        }
        
        // Set paper size and orientation
        $pdf->setPaper('A4', 'portrait');
        
        // Return PDF download
        return $pdf->download($filename);
    }
}
