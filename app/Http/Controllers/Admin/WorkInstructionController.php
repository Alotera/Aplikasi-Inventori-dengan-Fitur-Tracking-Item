<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\WorkInstruction;
use App\Models\Item;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class WorkInstructionController extends Controller
{
    public function index(): View
    {
        $workInstructions = WorkInstruction::with(['assignedUser', 'items', 'statusProgress'])
            ->orderBy('created_at', 'desc')
            ->paginate(15);
        
        // Update status untuk semua WI
        foreach ($workInstructions as $wi) {
            $wi->updateStatus();
        }
        
        return view('admin.work-instructions.index', compact('workInstructions'));
    }

    public function create(): View
    {
        $users = User::where('role', 'user')->where('is_active', true)->get();
        $items = Item::where('is_active', true)->get();
        
        return view('admin.work-instructions.create', compact('users', 'items'));
    }

    public function store(Request $request): RedirectResponse
    {
        $validated = $request->validate([
            'wi_number' => 'required|string|max:50|unique:work_instructions',
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
            'destination_line.required_if' => 'Tujuan line produksi wajib diisi untuk Work Instruction tipe "Ambil".',
            'dropoff_notes.max' => 'Catatan pengiriman tidak boleh lebih dari 1000 karakter.',
            'wi_number.unique' => 'Nomor WI sudah digunakan. Gunakan nomor yang berbeda.',
            'deadline.after' => 'Deadline harus lebih dari waktu sekarang.',
            'items.min' => 'Minimal harus ada 1 item dalam Work Instruction.',
        ]);

        // Check for duplicate items
        $itemIds = collect($validated['items'])->pluck('item_id')->toArray();
        if (count($itemIds) !== count(array_unique($itemIds))) {
            return redirect()->back()
                ->withInput()
                ->withErrors(['items' => 'Tidak boleh ada item yang duplikat dalam satu Work Instruction.']);
        }

        $workInstruction = WorkInstruction::create([
            'wi_number' => $validated['wi_number'],
            'type' => $validated['type'],
            'title' => $validated['title'],
            'description' => $validated['description'],
            'destination_line' => $validated['destination_line'] ?? null,
            'dropoff_notes' => $validated['dropoff_notes'] ?? null,
            'assigned_user_id' => $validated['assigned_user_id'],
            'deadline' => $validated['deadline'],
        ]);

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

        return redirect()->route('admin.work-instructions.index')
                        ->with('success', 'Work Instruction berhasil dibuat!');
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
        $workInstruction->load(['items', 'statusProgress']);
        
        return view('admin.work-instructions.edit', compact('workInstruction', 'users', 'items'));
    }

    public function update(Request $request, WorkInstruction $workInstruction): RedirectResponse
    {
        $validated = $request->validate([
            'wi_number' => 'required|string|max:50|unique:work_instructions,wi_number,' . $workInstruction->id,
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
            'destination_line.required_if' => 'Tujuan line produksi wajib diisi untuk Work Instruction tipe "Ambil".',
            'dropoff_notes.max' => 'Catatan pengiriman tidak boleh lebih dari 1000 karakter.',
            'wi_number.unique' => 'Nomor WI sudah digunakan. Gunakan nomor yang berbeda.',
            'items.min' => 'Minimal harus ada 1 item dalam Work Instruction.',
        ]);

        $workInstruction->update([
            'wi_number' => $validated['wi_number'],
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

        // Update status progress
        $workInstruction->updateStatus();

        return redirect()->route('admin.work-instructions.index')
                        ->with('success', 'Work Instruction berhasil diperbarui!');
    }

    public function destroy(WorkInstruction $workInstruction): RedirectResponse
    {
        $workInstruction->delete();
        return redirect()->route('admin.work-instructions.index')
                        ->with('success', 'Work Instruction berhasil dihapus!');
    }
}