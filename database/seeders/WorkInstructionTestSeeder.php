<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\WorkInstruction;
use App\Models\Item;
use App\Models\User;
use Carbon\Carbon;

class WorkInstructionTestSeeder extends Seeder
{
    public function run(): void
    {
        $user = User::where('role', 'user')->first();
        $items = Item::take(2)->get();

        if ($user && $items->count() > 0) {
            // WI dengan deadline lewat (kemarin)
            $overdueWI = WorkInstruction::firstOrCreate(
                ['wi_number' => 'WI-OVERDUE-001'],
                [
                'type' => 'checking',
                'title' => 'Overdue Work Instruction Test',
                'description' => 'Test WI dengan deadline yang sudah lewat',
                'assigned_user_id' => $user->id,
                'deadline' => Carbon::yesterday()->setTime(14, 0), // Kemarin jam 14:00
                'status' => 'not_started',
                ]
            );

            // Attach items to overdue WI
            foreach ($items as $index => $item) {
                $overdueWI->items()->syncWithoutDetaching([
                    $item->id => ['required_quantity' => rand(5, 20)]
                ]);
            }

            // WI dengan deadline lewat (2 jam yang lalu)
            $overdueWI2 = WorkInstruction::firstOrCreate(
                ['wi_number' => 'WI-OVERDUE-002'],
                [
                'type' => 'ambil',
                'title' => 'Overdue Work Instruction Test 2',
                'description' => 'Test WI dengan deadline 2 jam yang lalu',
                'assigned_user_id' => $user->id,
                'deadline' => Carbon::now()->subHours(2), // 2 jam yang lalu
                'status' => 'not_started',
                ]
            );

            // Attach items to second overdue WI
            foreach ($items as $index => $item) {
                $overdueWI2->items()->syncWithoutDetaching([
                    $item->id => ['required_quantity' => rand(3, 15)]
                ]);
            }

            // Update status untuk trigger overdue logic dan status progress
            $overdueWI->updateStatus();
            $overdueWI2->updateStatus();
        }
    }
}
