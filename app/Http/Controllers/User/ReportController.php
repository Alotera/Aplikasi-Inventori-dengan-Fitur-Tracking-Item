<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Models\WorkInstruction;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function create(WorkInstruction $workInstruction): View
    {
        $this->authorizeWI($workInstruction);
        $workInstruction->load('items');

        return view('user.reports.create', [
            'wi' => $workInstruction,
        ]);
    }

    public function store(Request $request, WorkInstruction $workInstruction): RedirectResponse
    {
        $this->authorizeWI($workInstruction);

        $data = $request->validate([
            'summary' => ['required', 'string', 'max:1000'],
            'notes' => ['nullable', 'string', 'max:1000'],
        ]);

        $workInstruction->notes = trim(($workInstruction->notes ?? '') . PHP_EOL . 'User Report: ' . $data['summary'] . (!empty($data['notes']) ? ' | ' . $data['notes'] : ''));
        $workInstruction->status = 'completed';
        $workInstruction->save();

        return redirect()->route('user.work-instructions.show', $workInstruction)->with('success', 'Laporan berhasil dikirim.');
    }

    protected function authorizeWI(WorkInstruction $wi): void
    {
        if ($wi->assigned_user_id !== Auth::id()) {
            abort(403, 'Anda tidak berhak mengakses WI ini.');
        }
    }
}


