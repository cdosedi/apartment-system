<?php

namespace Database\Seeders;

use App\Http\Controllers\ElectricBillController;
use App\Models\ElectricBill;
use App\Models\Invoice;
use App\Models\Lease;
use App\Models\LeasePayment;
use App\Models\Receipt;
use App\Models\Room;
use App\Models\Tenant;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TenantLeaseSeeder extends Seeder
{
    public function run(): void
    {
        $today = Carbon::now();

        DB::statement('SET FOREIGN_KEY_CHECKS=0');
        Receipt::truncate();
        Invoice::truncate();
        LeasePayment::truncate();
        ElectricBill::truncate();
        Lease::truncate();
        Tenant::truncate();
        Room::truncate();
        DB::statement('SET FOREIGN_KEY_CHECKS=1');

        $admin = User::firstOrCreate(
            ['email' => 'admin@admin.com'],
            ['name' => 'Admin', 'password' => bcrypt('admin123'), 'is_admin' => true]
        );

        $this->createRooms();
        $this->createTenantsAndLeases($admin, $today);
        $this->createPaymentsForAllLeases($today);
        $this->createElectricBills($today);
        $this->applyElectricBillCalculations();
        $this->markFirstMonthAsPaid();

        if ($this->command) {
            $stats = $this->getStats();
            $this->command->info("✅ Data: {$stats['tenants']} tenants, {$stats['rooms_occupied']}/{$stats['rooms']} rooms | Active: {$stats['active_leases']} | Expired: {$stats['expired_leases']}");
            $this->command->info("   Payments: {$stats['payments']} | w/ Bills: {$stats['payments_with_bills']} | Invoices: {$stats['invoices']} | Receipts: {$stats['receipts']}");
        }
    }

    protected function createRooms(): void
    {
        for ($i = 1; $i <= 40; $i++) {
            Room::create([
                'room_number' => (string) $i,
                'bed_capacity' => 3,
                'status' => 'available',
            ]);
        }
    }

    protected function createTenantsAndLeases(User $admin, Carbon $today): void
    {
        $filipinoNames = [
            ['Carlos', 'Manalastas'], ['Roy', 'Dimaculangan'], ['Maria', 'Salcedo'],
            ['Elena', 'Gonzales'], ['Juan', 'Reodica'], ['Antonio', 'Lumbres'],
            ['Jose', 'Regalado'], ['Melchora', 'Alonzo'], ['Andres', 'Bautista'],
            ['Emilio', 'Aguillon'], ['Gabriela', 'Soriano'], ['Teresa', 'Mercado'],
            ['Francisco', 'Baltazar'], ['Juan', 'Lontoc'], ['Marcelo', 'Peralta'],
            ['Gregorio', 'Pascual'], ['Apolinario', 'Mendoza'], ['Diego', 'Santos'],
            ['Ramil', 'Lapuz'], ['Sultan', 'Karim'], ['Mariano', 'Prado'],
            ['Graciano', 'Javier'], ['Trinidad', 'Torres'], ['Agueda', 'Evangelista'],
            ['Pedro', 'Cruz'], ['Maria', 'Luna'], ['Rosa', 'Santos'], ['Miguel', 'Reyes'],
            ['Lucia', 'Flores'], ['Carmen', 'Vergara'], ['Barbara', 'Castro'], ['Patricia', 'Morales'],
            ['Jennifer', 'Martinez'], ['Catherine', 'Gomez'], ['Jonathan', 'Diaz'], ['Christian', 'Torres'],
        ];

        $roomConfigs = [
            1 => [['idx' => 0, 'start' => -18, 'dur' => 24], ['idx' => 3, 'start' => -12, 'dur' => 24], ['idx' => 6, 'start' => -3, 'dur' => 12]],
            2 => [['idx' => 1, 'start' => -17, 'dur' => 24], ['idx' => 4, 'start' => -11, 'dur' => 24], ['idx' => 7, 'start' => -4, 'dur' => 24]],
            3 => [['idx' => 2, 'start' => -16, 'dur' => 24], ['idx' => 5, 'start' => -10, 'dur' => 24], ['idx' => 8, 'start' => -2, 'dur' => 24], ['idx' => 20, 'start' => 0, 'dur' => 12]],
            4 => [['idx' => 9, 'start' => -15, 'dur' => 24], ['idx' => 10, 'start' => -9, 'dur' => 24], ['idx' => 21, 'start' => -1, 'dur' => 12]],
            5 => [['idx' => 11, 'start' => -14, 'dur' => 24], ['idx' => 12, 'start' => -8, 'dur' => 24], ['idx' => 22, 'start' => 0, 'dur' => 12]],
            6 => [['idx' => 13, 'start' => -13, 'dur' => 24], ['idx' => 14, 'start' => -7, 'dur' => 24], ['idx' => 23, 'start' => -1, 'dur' => 12]],
            7 => [['idx' => 15, 'start' => -20, 'dur' => 24], ['idx' => 16, 'start' => -14, 'dur' => 24], ['idx' => 17, 'start' => -5, 'dur' => 24]],
            8 => [['idx' => 18, 'start' => -6, 'dur' => 24], ['idx' => 19, 'start' => -1, 'dur' => 24]],
        ];

        $nameIdx = 0;
        foreach ($roomConfigs as $roomNum => $configs) {
            $room = Room::where('room_number', (string) $roomNum)->first();
            foreach ($configs as $cfg) {
                $name = $filipinoNames[$cfg['idx']];
                $fullName = $name[0].' '.$name[1];

                $tenant = Tenant::create([
                    'full_name' => $fullName,
                    'email' => strtolower($name[0]).'.'.strtolower($name[1]).($nameIdx + 1).'@test.com',
                    'contact_number' => '09'.rand(100000000, 999999999),
                    'address' => 'Philippines',
                    'status' => 'active',
                    'created_by' => $admin->id,
                    'emergency_contact_name' => 'Emergency Contact',
                    'emergency_contact_number' => '09111111111',
                ]);

                $startDate = $today->copy()->addMonths($cfg['start']);
                $endDate = $startDate->copy()->addMonths($cfg['dur'])->subDay();
                $status = $endDate->lt($today) ? 'expired' : 'active';
                $rent = 4500 + ($cfg['idx'] * 100) + rand(-50, 50);

                Lease::create([
                    'tenant_id' => $tenant->id,
                    'room_id' => $room->id,
                    'start_date' => $startDate,
                    'end_date' => $endDate,
                    'duration_months' => $cfg['dur'],
                    'monthly_rent' => $rent,
                    'status' => $status,
                    'pending_electric_debt' => 0,
                ]);

                $nameIdx++;
            }
        }

        for ($roomNum = 9; $roomNum <= 40; $roomNum++) {
            $room = Room::where('room_number', (string) $roomNum)->first();
            $numTenants = rand(1, 2);

            for ($t = 0; $t < $numTenants; $t++) {
                $names = ['Pedro', 'Juan', 'Maria', 'Jose', 'Ana', 'Lisa', 'Mike', 'Sarah'];
                $name = $names[array_rand($names)];

                $tenant = Tenant::create([
                    'full_name' => "$name Room$roomNum",
                    'email' => "room{$roomNum}_ten{$t}@test.com",
                    'contact_number' => '09'.rand(100000000, 999999999),
                    'address' => 'Philippines',
                    'status' => 'active',
                    'created_by' => $admin->id,
                    'emergency_contact_name' => 'Emergency',
                    'emergency_contact_number' => '09111111111',
                ]);

                $monthsAgo = rand(2, 15);
                $duration = [6, 12, 24][array_rand([6, 12, 24])];
                $startDate = $today->copy()->subMonths($monthsAgo);

                Lease::create([
                    'tenant_id' => $tenant->id,
                    'room_id' => $room->id,
                    'start_date' => $startDate,
                    'end_date' => $startDate->copy()->addMonths($duration)->subDay(),
                    'duration_months' => $duration,
                    'monthly_rent' => rand(4500, 5500),
                    'status' => 'active',
                    'pending_electric_debt' => 0,
                ]);
            }
        }
    }

    protected function createPaymentsForAllLeases(Carbon $today): void
    {
        $leases = Lease::with('tenant')->get();

        foreach ($leases as $lease) {
            $startDate = Carbon::parse($lease->start_date);
            $monthsDuration = $lease->duration_months;

            for ($i = 0; $i < $monthsDuration; $i++) {
                $dueDate = $startDate->copy()->addMonths($i);

                $isFirstMonth = ($i === 0);
                $isPast = $dueDate->isPast();
                $monthsAgoFromNow = $today->diffInMonths($dueDate);

                $status = 'pending';
                $isPaid = false;

                if ($isFirstMonth) {
                    $status = 'paid';
                    $isPaid = true;
                } elseif ($isPast) {
                    $rand = rand(1, 100);
                    if ($monthsAgoFromNow <= 2 && $rand <= 40) {
                        $status = 'paid';
                        $isPaid = true;
                    } elseif ($monthsAgoFromNow <= 4 && $rand <= 25) {
                        $status = 'paid';
                        $isPaid = true;
                    } elseif ($monthsAgoFromNow <= 6 && $rand <= 15) {
                        $status = 'paid';
                        $isPaid = true;
                    } else {
                        $status = 'overdue';
                    }
                }

                $payment = LeasePayment::create([
                    'lease_id' => $lease->id,
                    'due_date' => $dueDate,
                    'amount' => $lease->monthly_rent,
                    'electric_bill_amount' => 0,
                    'carried_over_debt' => 0,
                    'electric_bill_id' => null,
                    'status' => $status,
                    'paid_at' => $isPaid ? $dueDate->copy()->addDays(rand(1, 5)) : null,
                    'notes' => 'Coverage: '.$dueDate->format('M d, Y'),
                ]);

                Invoice::create([
                    'lease_payment_id' => $payment->id,
                    'invoice_number' => 'INV-'.strtoupper(substr(md5(uniqid()), 0, 8)),
                    'status' => $status,
                ]);

                if ($isPaid) {
                    Receipt::create([
                        'lease_payment_id' => $payment->id,
                        'payment_method' => 'cash',
                        'amount_paid' => $lease->monthly_rent,
                        'receipt_number' => 'REC-'.$dueDate->format('Y').'-'.str_pad($payment->id, 5, '0', STR_PAD_LEFT),
                    ]);
                }
            }
        }
    }

    protected function createElectricBills(Carbon $today): void
    {
        $startMonth = Carbon::parse('2024-09-01');
        $endMonth = $today->copy()->startOfMonth();

        foreach (Room::all() as $room) {
            $roomLeases = Lease::where('room_id', $room->id)->get();
            if ($roomLeases->isEmpty()) {
                continue;
            }

            $current = $startMonth->copy();
            $baseAmount = 2500 + ($room->id * 100);

            while ($current->lte($endMonth)) {
                if (! ElectricBill::where('room_id', $room->id)
                    ->where('billing_month', $current)
                    ->exists()) {
                    ElectricBill::create([
                        'room_id' => $room->id,
                        'billing_month' => $current->copy(),
                        'total_amount' => $baseAmount,
                    ]);
                }
                $baseAmount += rand(30, 80);
                $current->addMonth();
            }
        }
    }

    protected function applyElectricBillCalculations(): void
    {
        $controller = app(ElectricBillController::class);

        $bills = ElectricBill::orderBy('billing_month')->get();

        foreach ($bills as $bill) {
            $billingStart = $bill->billing_month->copy()->startOfMonth();
            $billingEnd = $billingStart->copy()->endOfMonth();

            $controller->calculateAndApplyBills(
                $bill->room,
                $billingStart,
                $billingEnd,
                $bill
            );
        }
    }

    protected function markFirstMonthAsPaid(): void
    {
        foreach (Lease::all() as $lease) {
            $payments = $lease->payments()->where('status', 'paid')->get();

            foreach ($payments as $payment) {
                $receipt = $payment->receipt;
                if ($receipt) {
                    $totalWithElectric = $payment->amount
                        + ($payment->electric_bill_amount ?? 0)
                        + ($payment->carried_over_debt ?? 0);
                    $receipt->update(['amount_paid' => $totalWithElectric]);
                }
            }
        }
    }

    protected function getStats(): array
    {
        return [
            'tenants' => Tenant::count(),
            'rooms' => Room::count(),
            'rooms_occupied' => Room::where('status', 'occupied')->count(),
            'active_leases' => Lease::where('status', 'active')->count(),
            'expired_leases' => Lease::where('status', 'expired')->count(),
            'payments' => LeasePayment::count(),
            'payments_with_bills' => LeasePayment::whereNotNull('electric_bill_id')->count(),
            'invoices' => Invoice::count(),
            'receipts' => Receipt::count(),
        ];
    }
}
