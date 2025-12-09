<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Enums\StockMovementType;
use App\Models\WorkInstruction;
use App\Models\Item;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class WorkInstructionController extends Controller
{
    public function index(): View
    {
        $workInstructions = WorkInstruction::where('assigned_user_id', auth()->id())
            ->with(['items.itemLocations', 'statusProgress'])
            ->orderBy('deadline', 'asc')
            ->get();
        
        // Update status untuk semua WI user
        foreach ($workInstructions as $wi) {
            $wi->updateStatus();
        }
        
        return view('user.work-instructions.index', compact('workInstructions'));
    }

    public function show(WorkInstruction $workInstruction): View
    {
        // Ensure user can only view their own work instructions
        if ($workInstruction->assigned_user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to work instruction.');
        }

        $workInstruction->load(['items.itemLocations', 'statusProgress']);
        
        // Auto-start WI if it's still not started and user accesses it
        if ($workInstruction->getMainStatus() === 'not_started' && $workInstruction->getProgressionStatus() === 'pending') {
            // Start the WI by updating progression status
            $workInstruction->updateStatusProgress();
        }
        
        // Update status
        $workInstruction->updateStatus();
        
        return view('user.work-instructions.show', compact('workInstruction'));
    }

    public function updateItem(Request $request, WorkInstruction $workInstruction): RedirectResponse
    {
        // Ensure user can only update their own work instructions
        if ($workInstruction->assigned_user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to work instruction.');
        }

        // Different validation for different types
        if ($workInstruction->type === 'ambil') {
            $validated = $request->validate([
                'item_id' => 'required|exists:items,id',
                'actual_quantity' => 'required|integer|min:0',
                'condition' => 'required|in:excellent,good,fair,damaged_light,damaged,not_found',
                'status' => 'required|in:completed,not_found',
                'notes' => 'nullable|string|max:1000',
                // Removed: 'item_taken' => 'required|boolean', - checkbox tidak mengirim value jika tidak dicentang
            ]);
        } else {
            $validated = $request->validate([
                'item_id' => 'required|exists:items,id',
                'actual_quantity' => 'required|integer|min:0',
                'condition' => 'required|in:good,not_good',
                'status' => 'required|in:completed,not_good',
                'notes' => 'nullable|string|max:1000',
            ]);
        }

        $item = Item::findOrFail($validated['item_id']);

        // For CHECKING type, auto-set status based on quantity comparison
        if ($workInstruction->type === 'checking') {
            $requiredQuantity = $workInstruction->items()
                ->wherePivot('item_id', $validated['item_id'])
                ->first()->pivot->required_quantity;
            
            // Auto-set status based on quantity comparison
            $autoStatus = ($validated['actual_quantity'] == $requiredQuantity) 
                ? 'completed' 
                : 'not_good';
            
            // Override user input with auto logic
            $validated['status'] = $autoStatus;
        }

        // Update the pivot table
        $workInstruction->items()->updateExistingPivot($validated['item_id'], [
            'actual_quantity' => $validated['actual_quantity'],
            'condition' => $validated['condition'],
            'status' => $validated['status'],
            'notes' => $validated['notes'],
        ]);

        // For CHECKING type, update item stock
        if ($workInstruction->type === 'checking' && $validated['status'] === 'completed') {
            $item->updateStock(
                $validated['actual_quantity'],
                StockMovementType::CHECKING_RESULT,
                $workInstruction->id,
                auth()->id(),
                'Stock check via Work Instruction: ' . $workInstruction->wi_number,
                $validated['notes']
            );
        }

        // Update work instruction status
        $workInstruction->updateStatus();

        $action = $workInstruction->type === 'checking' ? 'checked' : 'taken';
        return redirect()->back()->with('success', "Item successfully {$action}!");
    }

    public function complete(WorkInstruction $workInstruction): RedirectResponse
    {
        // Ensure user can only complete their own work instructions
        if ($workInstruction->assigned_user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to work instruction.');
        }

        if ($workInstruction->getProgressionStatus() !== 'in_progress') {
            return redirect()->back()->with('error', 'Work instruction is not in progress status.');
        }

        // Check if all items are finished (either completed or not_good)
        $pendingItems = $workInstruction->items()->wherePivot('status', 'pending')->count();
        if ($pendingItems > 0) {
            return redirect()->back()->with('error', 'Please complete all items before finishing the work instruction.');
        }

        // Update status will automatically set to completed when all items are done
        $workInstruction->updateStatus();

        return redirect()->back()->with('success', 'Work instruction completed successfully!');
    }
}