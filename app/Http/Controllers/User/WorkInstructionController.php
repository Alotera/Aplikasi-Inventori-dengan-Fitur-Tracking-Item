<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Enums\StockMovementType;
use App\Models\WorkInstruction;
use App\Models\Item;
use App\Support\AppNotifier;
use App\Support\WiEvidenceStorage;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Validation\ValidationException;

class WorkInstructionController extends Controller
{
    public function index(Request $request): View
    {
        $validated = $request->validate([
            'type' => 'nullable|in:checking,ambil',
            'status' => 'nullable|in:not_started,completed,overdue',
        ]);

        $workInstructions = WorkInstruction::query()
            ->where('assigned_user_id', auth()->id())
            ->with(['items.itemLocations', 'statusProgress'])
            ->when(! empty($validated['type'] ?? null), fn ($q) => $q->where('type', $validated['type']))
            ->when(! empty($validated['status'] ?? null), fn ($q) => $q->where('status', $validated['status']))
            ->orderByDesc('id')
            ->get();

        foreach ($workInstructions as $wi) {
            $wi->updateStatus();
        }

        return view('user.work-instructions.index', compact('workInstructions'));
    }

    public function show(WorkInstruction $workInstruction): View
    {
        // Ensure user can only view their own work instructions
        if ($workInstruction->assigned_user_id !== auth()->id()) {
            abort(403, __('errors.unauthorized_wi'));
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
            abort(403, __('errors.unauthorized_wi'));
        }

        if ($request->boolean('discrepancy_upload_only')) {
            $data = $request->validate([
                'item_id' => 'required|exists:items,id',
                'discrepancy_photo' => 'required|image|mimes:jpeg,jpg,png,webp|max:5120',
            ]);

            $attached = $workInstruction->items()->where('items.id', $data['item_id'])->first();
            if (! $attached || ! in_array($attached->pivot->status, ['not_good', 'not_found'], true)) {
                return redirect()->back()->with('error', __('user.wi_evidence.discrepancy_upload_invalid'));
            }

            WiEvidenceStorage::deleteIfExists($attached->pivot->discrepancy_evidence_path);
            $path = WiEvidenceStorage::storeItemDiscrepancy($request->file('discrepancy_photo'), $workInstruction->id);
            $workInstruction->items()->updateExistingPivot($data['item_id'], [
                'discrepancy_evidence_path' => $path,
            ]);

            return redirect()->back()->with('success', __('user.wi_evidence.discrepancy_saved'));
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

        $pivotRow = $workInstruction->items()->where('items.id', $validated['item_id'])->first();
        if (! $pivotRow) {
            throw ValidationException::withMessages(['item_id' => __('errors.unauthorized_wi')]);
        }
        $existingEvidencePath = $pivotRow->pivot->discrepancy_evidence_path;

        // For CHECKING type: Discrepancy condition always yields not_good; else derive from quantity match.
        if ($workInstruction->type === 'checking') {
            $requiredQuantity = (int) $pivotRow->pivot->required_quantity;
            $actualQty = (int) $validated['actual_quantity'];
            if ($validated['condition'] === 'not_good') {
                $validated['status'] = 'not_good';
            } elseif ($actualQty !== $requiredQuantity) {
                $validated['status'] = 'not_good';
            } else {
                $validated['status'] = 'completed';
            }
        }

        if (in_array($validated['status'], ['not_good', 'not_found'], true)) {
            $photoRules = ['discrepancy_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120']];
            if (empty($existingEvidencePath)) {
                $photoRules['discrepancy_photo'] = ['required', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'];
            }
            $request->validate($photoRules);
        } else {
            $request->validate([
                'discrepancy_photo' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
            ]);
        }

        $pivotUpdate = [
            'actual_quantity' => $validated['actual_quantity'],
            'condition' => $validated['condition'],
            'status' => $validated['status'],
            'notes' => $validated['notes'] ?? null,
        ];

        if (in_array($validated['status'], ['not_good', 'not_found'], true)) {
            if ($request->hasFile('discrepancy_photo')) {
                WiEvidenceStorage::deleteIfExists($existingEvidencePath);
                $pivotUpdate['discrepancy_evidence_path'] = WiEvidenceStorage::storeItemDiscrepancy(
                    $request->file('discrepancy_photo'),
                    $workInstruction->id
                );
            } else {
                $pivotUpdate['discrepancy_evidence_path'] = $existingEvidencePath;
            }
        } else {
            WiEvidenceStorage::deleteIfExists($existingEvidencePath);
            $pivotUpdate['discrepancy_evidence_path'] = null;
        }

        $workInstruction->items()->updateExistingPivot($validated['item_id'], $pivotUpdate);

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

        $workInstruction->refresh();
        AppNotifier::workInstructionAdminActivity('item_done', $workInstruction, auth()->user()->name, $item->name);

        return redirect()->back()->with('success', $workInstruction->type === 'checking'
            ? __('messages.wi_item_checked')
            : __('messages.wi_item_taken'));
    }

    public function complete(Request $request, WorkInstruction $workInstruction): RedirectResponse
    {
        // Ensure user can only complete their own work instructions
        if ($workInstruction->assigned_user_id !== auth()->id()) {
            abort(403, __('errors.unauthorized_wi'));
        }

        if ($workInstruction->getProgressionStatus() !== 'in_progress') {
            return redirect()->back()->with('error', __('messages.wi_not_in_progress'));
        }

        // Check if all items are finished (either completed or not_good)
        $pendingItems = $workInstruction->items()->wherePivot('status', 'pending')->count();
        if ($pendingItems > 0) {
            return redirect()->back()->with('error', __('messages.wi_complete_items_first'));
        }

        $request->validate([
            'completion_evidence' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:5120'],
        ]);

        if (! $request->hasFile('completion_evidence') && empty($workInstruction->completion_evidence_path)) {
            throw ValidationException::withMessages([
                'completion_evidence' => __('user.wi_evidence.completion_required'),
            ]);
        }

        $workInstruction->load('items');
        foreach ($workInstruction->items as $wiItem) {
            if (in_array($wiItem->pivot->status, ['not_good', 'not_found'], true)
                && empty($wiItem->pivot->discrepancy_evidence_path)) {
                throw ValidationException::withMessages([
                    'completion_evidence' => __('user.wi_evidence.missing_item_photos'),
                ]);
            }
        }

        if ($request->hasFile('completion_evidence')) {
            WiEvidenceStorage::deleteIfExists($workInstruction->completion_evidence_path);
            $workInstruction->completion_evidence_path = WiEvidenceStorage::storeCompletion(
                $request->file('completion_evidence'),
                $workInstruction->id
            );
            $workInstruction->save();
        }

        // Update status will automatically set to completed when all items are done
        $workInstruction->updateStatus();

        $workInstruction->refresh();
        AppNotifier::workInstructionAdminActivity('wi_done', $workInstruction, auth()->user()->name);

        return redirect()->back()->with('success', __('messages.wi_complete'));
    }
}