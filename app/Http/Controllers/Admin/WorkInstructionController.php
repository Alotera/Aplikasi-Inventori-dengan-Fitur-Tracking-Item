<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkInstruction;
use App\Models\Item;
use App\Models\User;
use App\Models\StockMovement;
use App\Models\ProductionLine;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use App\Support\AppNotifier;
use Illuminate\Support\Facades\Storage;

class WorkInstructionController extends Controller
{
    /**
     * Generate immutable WI number.
     *
     * Format:
     * - checking -> CHK-YY-MMDD-SEQ
     * - ambil    -> AMB-YY-MMDD-SEQ
     * - SEQ resets monthly (based on YY + MM), while MMDD keeps the current day token.
     */
    private function generateWiNumber(string $type): string
    {
        $prefix = match ($type) {
            'checking' => 'CHK',
            'ambil' => 'AMB',
            default => 'WI',
        };

        $now = now();
        $yy = $now->format('y'); // 2 digit year
        $mm = $now->format('m'); // 2 digit month
        $mmdd = $now->format('md'); // 4 digits MMDD

        // Group for monthly reset: PREFIX-YY-MM****
        $likePrefix = "{$prefix}-{$yy}-{$mm}";
        $regex = "/^" . preg_quote($likePrefix, '/') . "\\d{2}-(\\d{4})$/";

        $existingWiNumbers = WorkInstruction::where('wi_number', 'like', "{$likePrefix}%")
            ->pluck('wi_number');

        $maxSeq = 0;
        foreach ($existingWiNumbers as $wiNumber) {
            if (preg_match($regex, $wiNumber, $matches)) {
                $maxSeq = max($maxSeq, (int) $matches[1]);
            }
        }

        $nextSeq = $maxSeq + 1;
        $seqPadded = str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$yy}-{$mmdd}-{$seqPadded}";
    }

    public function index(Request $request): View
    {
        $validated = $request->validate([
            'assigned_user_id' => 'nullable|integer|exists:users,id',
            'type' => 'nullable|in:checking,ambil',
            'status' => 'nullable|in:not_started,completed,overdue',
        ]);

        $assignedUserId = $validated['assigned_user_id'] ?? null;
        if ($assignedUserId !== null) {
            $assigneeOk = User::query()
                ->where('id', $assignedUserId)
                ->where('role', 'user')
                ->where('is_active', true)
                ->exists();
            if (! $assigneeOk) {
                $assignedUserId = null;
            }
        }

        $query = WorkInstruction::with(['assignedUser', 'items', 'statusProgress'])
            ->when($assignedUserId !== null, fn ($q) => $q->where('assigned_user_id', $assignedUserId))
            ->when(! empty($validated['type'] ?? null), fn ($q) => $q->where('type', $validated['type']))
            ->when(! empty($validated['status'] ?? null), fn ($q) => $q->where('status', $validated['status']))
            ->orderBy('created_at', 'desc');

        $workInstructions = $query->paginate(15)->appends($request->only(['assigned_user_id', 'type', 'status']));

        foreach ($workInstructions as $wi) {
            $wi->updateStatus();
        }

        $filterUsers = User::query()
            ->where('role', 'user')
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('admin.work-instructions.index', compact('workInstructions', 'filterUsers'));
    }

    public function create(): View
    {
        $users = User::where('role', 'user')->where('is_active', true)->get();
        $items = Item::where('is_active', true)->get();
        $productionLines = ProductionLine::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();
        
        return view('admin.work-instructions.create', compact('users', 'items', 'productionLines'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:checking,ambil',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'destination_line' => 'nullable|required_if:type,ambil|string|max:255',
            'dropoff_notes' => 'nullable|string|max:1000',
            'assigned_user_id' => 'required|exists:users,id',
            'deadline' => 'required|date|after:now',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.required_quantity' => 'required|integer|min:1',
        ], [
            'destination_line.required_if' => __('messages.wi_validation.destination_line_required'),
            'dropoff_notes.max' => __('messages.wi_validation.dropoff_notes_max'),
            'deadline.after' => __('messages.wi_validation.deadline_after'),
            'items.min' => __('messages.wi_validation.items_min'),
        ]);

        // Check for duplicate items
        $itemIds = collect($validated['items'])->pluck('item_id')->toArray();
        if (count($itemIds) !== count(array_unique($itemIds))) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['items' => __('messages.wi_validation.duplicate_items')]);
        }

        // Create WI with an auto-generated immutable wi_number.
        $workInstruction = null;
        $attempts = 0;
        while ($workInstruction === null && $attempts < 5) {
            $attempts++;
            $generatedWiNumber = $this->generateWiNumber($validated['type']);

            try {
                $workInstruction = WorkInstruction::create([
                    'wi_number' => $generatedWiNumber,
                    'type' => $validated['type'],
                    'title' => $validated['title'],
                    'description' => $validated['description'],
                    'destination_line' => $validated['destination_line'] ?? null,
                    'dropoff_notes' => $validated['dropoff_notes'] ?? null,
                    'assigned_user_id' => $validated['assigned_user_id'],
                    'deadline' => $validated['deadline'],
                ]);
            } catch (QueryException $e) {
                // Retry only on unique constraint collisions for wi_number.
                if (stripos($e->getMessage(), 'wi_number') === false || stripos($e->getMessage(), 'unique') === false) {
                    throw $e;
                }
            }
        }

        if ($workInstruction === null) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['wi_number' => __('messages.wi_validation.wi_number_failed')]);
        }

        // Sync items with quantities (handles duplicates automatically)
        $itemsData = [];
        foreach ($validated['items'] as $itemData) {
            $itemsData[$itemData['item_id']] = [
                'required_quantity' => $itemData['required_quantity']
            ];
        }
        $workInstruction->items()->sync($itemsData);

        // For AMBIL type, automatically reduce stock when WI is created
        if ($workInstruction->type === 'ambil') {
            foreach ($validated['items'] as $itemData) {
                $item = Item::find($itemData['item_id']);
                if ($item && $item->current_stock >= $itemData['required_quantity']) {
                    // Reduce stock immediately when WI is created
                    $beforeStock = $item->current_stock;
                    $item->current_stock -= $itemData['required_quantity'];
                    $item->save();
                    
                    // Create stock movement record
                    \App\Models\StockMovement::create([
                        'item_id' => $item->id,
                        'movement_type' => 'WI_CONSUMPTION',
                        'quantity' => -$itemData['required_quantity'],
                        'before_quantity' => $beforeStock,
                        'after_quantity' => $item->current_stock,
                        'reference_type' => 'work_instruction',
                        'reference_id' => $workInstruction->id,
                        'location_id' => null,
                        'user_id' => $workInstruction->assigned_user_id,
                        'notes' => 'Work Instruction Ambil: ' . $workInstruction->wi_number . ' - Barang disiapkan untuk ' . ($workInstruction->destination_line ?? 'line produksi'),
                        'metadata' => [
                            'wi_number' => $workInstruction->wi_number,
                            'wi_type' => 'ambil',
                            'destination_line' => $workInstruction->destination_line,
                            'auto_reduced_on_creation' => true,
                        ],
                    ]);
                }
            }
        }

        // Update status progress
        $workInstruction->updateStatus();

        $workInstruction->refresh();
        AppNotifier::workInstructionAssigned($workInstruction);

        return redirect()->route('admin.work-instructions.index')
                        ->with('success', __('messages.wi.created'));
    }

    public function show(WorkInstruction $workInstruction): View
    {
        $workInstruction->load(['assignedUser', 'items', 'statusProgress']);
        
        // Update status
        $workInstruction->updateStatus();
        
        return view('admin.work-instructions.show', compact('workInstruction'));
    }

    public function edit(WorkInstruction $workInstruction): View
    {
        $users = User::where('role', 'user')->where('is_active', true)->get();
        $items = Item::where('is_active', true)->get();
        $productionLines = ProductionLine::query()
            ->where('is_active', true)
            ->orderBy('name')
            ->get();

        // If the saved destination line is inactive, keep it visible in the dropdown.
        if (! empty($workInstruction->destination_line)) {
            $current = ProductionLine::query()
                ->where('name', $workInstruction->destination_line)
                ->first();

            if ($current && ! $productionLines->contains(fn ($pl) => $pl->name === $current->name)) {
                $productionLines->push($current);
            }
        }
        $workInstruction->load(['items', 'statusProgress']);
        
        return view('admin.work-instructions.edit', compact('workInstruction', 'users', 'items', 'productionLines'));
    }

    public function update(Request $request, WorkInstruction $workInstruction): RedirectResponse
    {
        $validated = $request->validate([
            'type' => 'required|in:checking,ambil',
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'destination_line' => 'nullable|required_if:type,ambil|string|max:255',
            'dropoff_notes' => 'nullable|string|max:1000',
            'assigned_user_id' => 'required|exists:users,id',
            'deadline' => 'required|date',
            'items' => 'required|array|min:1',
            'items.*.item_id' => 'required|exists:items,id',
            'items.*.required_quantity' => 'required|integer|min:1',
        ], [
            'destination_line.required_if' => __('messages.wi_validation.destination_line_required'),
            'dropoff_notes.max' => __('messages.wi_validation.dropoff_notes_max'),
            'items.min' => __('messages.wi_validation.items_min'),
        ]);

        $previousAssigneeId = (int) $workInstruction->assigned_user_id;
        $previousWiNumber = $workInstruction->wi_number;
        $previousTitle = $workInstruction->title;

        $oldItems = $workInstruction->items()
            ->get()
            ->mapWithKeys(fn ($item) => [(int) $item->id => (int) $item->pivot->required_quantity])
            ->toArray();

        $workInstruction->update([
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'destination_line' => $validated['destination_line'] ?? null,
            'dropoff_notes' => $validated['dropoff_notes'] ?? null,
            'assigned_user_id' => $validated['assigned_user_id'],
            'deadline' => $validated['deadline'],
        ]);

        // Sync items
        $itemsData = [];
        foreach ($validated['items'] as $itemData) {
            $itemsData[$itemData['item_id']] = [
                'required_quantity' => $itemData['required_quantity']
            ];
        }
        $workInstruction->items()->sync($itemsData);

        $newItems = $workInstruction->items()
            ->get()
            ->mapWithKeys(fn ($item) => [(int) $item->id => (int) $item->pivot->required_quantity])
            ->toArray();

        // For AMBIL type, adjust stock based on quantity differences
        if ($workInstruction->type === 'ambil') {
            $allItemIds = array_unique(array_merge(array_keys($oldItems), array_keys($newItems)));

            foreach ($allItemIds as $itemId) {
                $oldQty = $oldItems[$itemId] ?? 0;
                $newQty = $newItems[$itemId] ?? 0;
                $delta = $newQty - $oldQty;

                if ($delta === 0) {
                    continue;
                }

                $item = Item::find($itemId);
                if (!$item) {
                    continue;
                }

                $beforeStock = $item->current_stock;

                if ($delta > 0) {
                    // Increase required qty => additional consumption from stock
                    $item->current_stock -= $delta;
                    $movementQuantity = -$delta;
                } else {
                    // Decrease required qty => return stock
                    $restore = abs($delta);
                    $item->current_stock += $restore;
                    $movementQuantity = $restore;
                }

                $item->save();

                StockMovement::create([
                    'item_id' => $item->id,
                    'movement_type' => 'WI_CONSUMPTION',
                    'quantity' => $movementQuantity,
                    'before_quantity' => $beforeStock,
                    'after_quantity' => $item->current_stock,
                    'reference_type' => 'work_instruction',
                    'reference_id' => $workInstruction->id,
                    'location_id' => null,
                    'user_id' => $workInstruction->assigned_user_id,
                    'notes' => 'Adjustment after editing Work Instruction: ' . $workInstruction->wi_number,
                    'metadata' => [
                        'wi_number' => $workInstruction->wi_number,
                        'wi_type' => 'ambil',
                        'quantity_delta' => $delta,
                        'auto_adjusted_on_edit' => true,
                    ],
                ]);
            }
        }

        // Update status progress
        $workInstruction->updateStatus();

        $workInstruction->refresh();
        AppNotifier::workInstructionReassigned(
            $workInstruction,
            $previousAssigneeId,
            $previousWiNumber,
            $previousTitle
        );

        return redirect()->route('admin.work-instructions.index')
                        ->with('success', __('messages.wi.updated'));
    }

    public function destroy(WorkInstruction $workInstruction): RedirectResponse
    {
        AppNotifier::workInstructionDeleted(
            (int) $workInstruction->assigned_user_id,
            $workInstruction->wi_number,
            $workInstruction->title
        );

        $workInstruction->delete();
        return redirect()->route('admin.work-instructions.index')
                        ->with('success', __('messages.wi.deleted'));
    }
}