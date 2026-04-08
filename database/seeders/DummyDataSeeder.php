<?php

namespace Database\Seeders;

use App\Models\Item;
use App\Models\ItemLocation;
use App\Models\Location;
use App\Models\ProductionLine;
use App\Models\StockMovement;
use App\Models\User;
use App\Models\WorkInstruction;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

/**
 * Clears application test/business data and inserts consistent dummy records.
 * Does not create, update, or delete users — requires at least one existing user for FKs.
 */
class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        if (User::query()->count() === 0) {
            $this->command?->error(
                'DummyDataSeeder skipped: no users in the database. Create at least one user first, then run: php artisan db:seed --class=DummyDataSeeder'
            );

            return;
        }

        $assigneeId = User::query()
            ->where('role', 'user')
            ->where('is_active', true)
            ->value('id')
            ?? User::query()->value('id');

        $actorId = User::query()
            ->whereIn('role', ['admin', 'warehouse_staff'])
            ->where('is_active', true)
            ->value('id')
            ?? User::query()->value('id');

        DB::transaction(function () use ($assigneeId, $actorId) {
            $this->purgeNonUserData();

            $productionLines = $this->seedProductionLines();
            $this->seedLocations();
            $items = $this->seedItemsAndLocations();
            $this->seedStockMovements($items, $actorId);
            $this->seedWorkInstructions($items, $assigneeId, $productionLines);
        });

        $this->command?->info('Dummy data seeded (users table untouched).');
    }

    /**
     * Remove all application data except users and auth-related tables.
     */
    private function purgeNonUserData(): void
    {
        DB::table('notifications')->delete();
        StockMovement::query()->delete();
        WorkInstruction::query()->delete();
        Item::query()->delete();
        Location::query()->delete();
        ProductionLine::query()->delete();
    }

    /**
     * @return list<ProductionLine>
     */
    private function seedProductionLines(): array
    {
        $names = ['Assembly Line A', 'Assembly Line B', 'Packaging', 'QC Line', 'Shipping Prep'];

        $out = [];
        foreach ($names as $name) {
            $out[] = ProductionLine::query()->create([
                'name' => $name,
                'is_active' => true,
            ]);
        }

        return $out;
    }

    private function seedLocations(): void
    {
        $rows = [
            ['name' => 'Gudang Utama', 'zone' => 'Z1', 'rack' => 'A', 'row' => '01'],
            ['name' => 'Gudang Utama', 'zone' => 'Z1', 'rack' => 'A', 'row' => '02'],
            ['name' => 'Gudang Utama', 'zone' => 'Z2', 'rack' => 'B', 'row' => '01'],
            ['name' => 'Cold Storage', 'zone' => 'CS', 'rack' => 'C', 'row' => '01'],
            ['name' => 'Staging', 'zone' => 'ST', 'rack' => 'S', 'row' => '01'],
        ];

        foreach ($rows as $row) {
            Location::query()->create([
                'name' => $row['name'],
                'zone' => $row['zone'],
                'rack' => $row['rack'],
                'row' => $row['row'],
                'is_active' => true,
            ]);
        }
    }

    /**
     * @return list<Item>
     */
    private function seedItemsAndLocations(): array
    {
        $defs = [
            ['item_code' => 'DMY-001', 'name' => 'Laptop Dell Inspiron', 'category' => 'Electronics', 'current_stock' => 24, 'minimum_stock' => 5, 'unit' => 'pcs'],
            ['item_code' => 'DMY-002', 'name' => 'Mouse Wireless', 'category' => 'Accessories', 'current_stock' => 80, 'minimum_stock' => 10, 'unit' => 'pcs'],
            ['item_code' => 'DMY-003', 'name' => 'Keyboard Mechanical', 'category' => 'Accessories', 'current_stock' => 30, 'minimum_stock' => 8, 'unit' => 'pcs'],
            ['item_code' => 'DMY-004', 'name' => 'Monitor 24 inch', 'category' => 'Electronics', 'current_stock' => 15, 'minimum_stock' => 3, 'unit' => 'pcs'],
            ['item_code' => 'DMY-005', 'name' => 'Kabel HDMI 2m', 'category' => 'Cables', 'current_stock' => 120, 'minimum_stock' => 20, 'unit' => 'pcs'],
            ['item_code' => 'DMY-006', 'name' => 'Webcam USB', 'category' => 'Electronics', 'current_stock' => 40, 'minimum_stock' => 5, 'unit' => 'pcs'],
            ['item_code' => 'DMY-007', 'name' => 'Headset Office', 'category' => 'Accessories', 'current_stock' => 55, 'minimum_stock' => 10, 'unit' => 'pcs'],
            ['item_code' => 'DMY-008', 'name' => 'SSD 512GB', 'category' => 'Storage', 'current_stock' => 18, 'minimum_stock' => 4, 'unit' => 'pcs'],
        ];

        $items = [];
        foreach ($defs as $i => $def) {
            $item = Item::query()->create([
                'item_code' => $def['item_code'],
                'name' => $def['name'],
                'description' => 'Contoh barang dummy untuk demonstrasi.',
                'category' => $def['category'],
                'current_stock' => $def['current_stock'],
                'minimum_stock' => $def['minimum_stock'],
                'unit' => $def['unit'],
                'is_active' => true,
            ]);

            ItemLocation::query()->create([
                'item_id' => $item->id,
                'location_name' => 'Gudang Utama',
                'zone' => 'Z1',
                'rack' => 'R' . ($i % 3 + 1),
                'shelf' => 'S' . ($i % 4 + 1),
                'quantity' => $item->current_stock,
            ]);

            $items[] = $item;
        }

        return $items;
    }

    /**
     * @param  list<Item>  $items
     */
    private function seedStockMovements(array $items, int $actorId): void
    {
        $a = $items[0];
        $b = $items[1];

        $beforeA = $a->current_stock;
        $deltaIn = 12;
        StockMovement::query()->create([
            'item_id' => $a->id,
            'movement_type' => 'IN',
            'quantity' => $deltaIn,
            'before_quantity' => $beforeA - $deltaIn,
            'after_quantity' => $beforeA,
            'reference_type' => null,
            'reference_id' => null,
            'location_id' => null,
            'user_id' => $actorId,
            'notes' => 'Dummy: stock masuk awal',
            'metadata' => null,
        ]);

        $beforeB = $b->current_stock;
        $deltaOut = 5;
        StockMovement::query()->create([
            'item_id' => $b->id,
            'movement_type' => 'OUT',
            'quantity' => -$deltaOut,
            'before_quantity' => $beforeB + $deltaOut,
            'after_quantity' => $beforeB,
            'reference_type' => null,
            'reference_id' => null,
            'location_id' => null,
            'user_id' => $actorId,
            'notes' => 'Dummy: stock keluar',
            'metadata' => null,
        ]);

        StockMovement::query()->create([
            'item_id' => $items[2]->id,
            'movement_type' => 'ADJUSTMENT',
            'quantity' => 2,
            'before_quantity' => $items[2]->current_stock - 2,
            'after_quantity' => $items[2]->current_stock,
            'reference_type' => 'manual_adjustment',
            'reference_id' => null,
            'location_id' => null,
            'user_id' => $actorId,
            'notes' => 'Dummy: penyesuaian inventori',
            'metadata' => null,
        ]);
    }

    /**
     * @param  list<Item>  $items
     * @param  list<ProductionLine>  $productionLines
     */
    private function seedWorkInstructions(array $items, int $assigneeId, array $productionLines): void
    {
        $lineName = $productionLines[0]->name ?? 'Assembly Line A';

        // 1) Checking — belum dimulai
        $wi1 = $this->createWorkInstructionOrThrow([
            'type' => 'checking',
            'title' => 'Dummy: stock opname mingguan',
            'description' => 'Pemeriksaan stok contoh (dummy).',
            'destination_line' => null,
            'dropoff_notes' => null,
            'assigned_user_id' => $assigneeId,
            'deadline' => Carbon::now()->addDays(3)->setTime(17, 0),
            'status' => 'not_started',
        ], [
            $items[0]->id => ['required_quantity' => 10],
            $items[1]->id => ['required_quantity' => 20],
        ]);
        $wi1->updateStatus();

        // 2) Checking — satu item sudah selesai (in progress)
        $wi2 = $this->createWorkInstructionOrThrow([
            'type' => 'checking',
            'title' => 'Dummy: verifikasi sampling',
            'description' => 'Satu item sudah diproses (dummy).',
            'destination_line' => null,
            'dropoff_notes' => null,
            'assigned_user_id' => $assigneeId,
            'deadline' => Carbon::now()->addDay()->setTime(12, 0),
            'status' => 'not_started',
        ], [
            $items[0]->id => ['required_quantity' => 5],
            $items[2]->id => ['required_quantity' => 8],
        ]);
        $wi2->items()->updateExistingPivot($items[0]->id, [
            'actual_quantity' => 5,
            'condition' => 'good',
            'status' => 'completed',
            'notes' => 'Dummy: sesuai',
        ]);
        $wi2->refresh();
        $wi2->load('items');
        $wi2->updateStatus();

        // 3) Ambil — mengurangi stok seperti alur admin store
        $qtyA = 4;
        $qtyB = 6;
        $wi3 = $this->createWorkInstructionOrThrow([
            'type' => 'ambil',
            'title' => 'Dummy: pengambilan ke line produksi',
            'description' => 'Ambil barang untuk produksi (dummy).',
            'destination_line' => $lineName,
            'dropoff_notes' => 'Antar ke area staging.',
            'assigned_user_id' => $assigneeId,
            'deadline' => Carbon::now()->addDays(2)->setTime(16, 0),
            'status' => 'not_started',
        ], [
            $items[3]->id => ['required_quantity' => $qtyA],
            $items[4]->id => ['required_quantity' => $qtyB],
        ]);

        foreach ([[$items[3], $qtyA], [$items[4], $qtyB]] as [$item, $qty]) {
            $item->refresh();
            if ($item->current_stock < $qty) {
                continue;
            }
            $beforeStock = $item->current_stock;
            $item->current_stock -= $qty;
            $item->save();

            ItemLocation::query()->where('item_id', $item->id)->first()?->update([
                'quantity' => $item->current_stock,
            ]);

            StockMovement::query()->create([
                'item_id' => $item->id,
                'movement_type' => 'WI_CONSUMPTION',
                'quantity' => -$qty,
                'before_quantity' => $beforeStock,
                'after_quantity' => $item->current_stock,
                'reference_type' => 'work_instruction',
                'reference_id' => $wi3->id,
                'location_id' => null,
                'user_id' => $assigneeId,
                'notes' => 'Work Instruction Ambil: ' . $wi3->wi_number . ' - Barang disiapkan untuk ' . $lineName,
                'metadata' => [
                    'wi_number' => $wi3->wi_number,
                    'wi_type' => 'ambil',
                    'destination_line' => $lineName,
                    'auto_reduced_on_creation' => true,
                ],
            ]);
        }

        $wi3->refresh();
        $wi3->updateStatus();
    }

    /**
     * Mirrors Admin\\WorkInstructionController::generateWiNumber (immutable monthly sequence).
     */
    private function generateWiNumber(string $type): string
    {
        $prefix = match ($type) {
            'checking' => 'CHK',
            'ambil' => 'AMB',
            default => 'WI',
        };

        $now = now();
        $yy = $now->format('y');
        $mm = $now->format('m');
        $mmdd = $now->format('md');

        $likePrefix = "{$prefix}-{$yy}-{$mm}";
        $regex = '/^' . preg_quote($likePrefix, '/') . '\d{2}-(\d{4})$/';

        $existingWiNumbers = WorkInstruction::query()
            ->where('wi_number', 'like', "{$likePrefix}%")
            ->pluck('wi_number');

        $maxSeq = 0;
        foreach ($existingWiNumbers as $wiNumber) {
            if (preg_match($regex, (string) $wiNumber, $matches)) {
                $maxSeq = max($maxSeq, (int) $matches[1]);
            }
        }

        $nextSeq = $maxSeq + 1;
        $seqPadded = str_pad((string) $nextSeq, 4, '0', STR_PAD_LEFT);

        return "{$prefix}-{$yy}-{$mmdd}-{$seqPadded}";
    }

    /**
     * @param  array<string, mixed>  $attributes
     * @param  array<int, array<string, int>>  $itemsSync
     */
    private function createWorkInstructionOrThrow(array $attributes, array $itemsSync): WorkInstruction
    {
        $type = $attributes['type'];
        $attempts = 0;
        $workInstruction = null;

        while ($workInstruction === null && $attempts < 8) {
            $attempts++;
            $generatedWiNumber = $this->generateWiNumber($type);

            try {
                $workInstruction = WorkInstruction::query()->create(array_merge($attributes, [
                    'wi_number' => $generatedWiNumber,
                ]));
            } catch (QueryException $e) {
                if (stripos($e->getMessage(), 'wi_number') === false || stripos($e->getMessage(), 'unique') === false) {
                    throw $e;
                }
            }
        }

        if ($workInstruction === null) {
            throw new \RuntimeException('Failed to generate a unique wi_number for dummy data.');
        }

        $workInstruction->items()->sync($itemsSync);

        return $workInstruction->refresh();
    }
}
