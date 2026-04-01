<?php

namespace Database\Seeders;

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
        DB::transaction(function () {

            $admin = User::firstOrCreate(['email' => 'admin@admin.com'], ['name' => 'Admin', 'password' => bcrypt('admin123'), 'is_admin' => true]);
            Room::query()->delete();
            for ($i = 1; $i <= 40; $i++) {
                Room::create(['room_number' => (string) $i, 'bed_capacity' => 3, 'status' => 'available']);
            }

            $rooms = Room::all();
            $today = Carbon::create(2026, 2, 12);
            $simStart = Carbon::create(2021, 1, 1);

            $names = [
                'Carlos Manalastas', 'Roy Dimaculangan', 'Maria Salcedo', 'Elena Gonzales', 'Juan Reodica', 'Antonio Lumbres', 'Jose Regalado', 'Melchora Alonzo', 'Andres Bautista', 'Emilio Aguillon',
                'Gabriela Soriano', 'Teresa Mercado', 'Francisco Baltazar', 'Juan Lontoc', 'Marcelo Peralta', 'Gregorio Pascual', 'Apolinario Mendoza', 'Diego Santos', 'Ramil Lapuz', 'Sultan Karim',
                'Panday Pineda', 'Mariano Prado', 'Graciano Javier', 'Trinidad Torres', 'Agueda Evangelista',

                'Ramon Villareal', 'Ferdinand Cabrera', 'Corazon Marquez', 'Fidel Sandoval', 'Joseph Lacsamana', 'Gloria Dizon', 'Benigno Tolentino', 'Rodrigo Villamor', 'Bongbong Alvarado', 'Sara Mangubat',
            ];

            foreach ($names as $index => $name) {
                $tenant = Tenant::create([
                    'full_name' => $name,
                    'email' => strtolower(str_replace(' ', '.', $name)).$index.'@test.com',
                    'contact_number' => '09'.rand(100000000, 999999999),
                    'address' => 'Philippines',
                    'status' => 'active',
                    'created_by' => $admin->id,
                    'emergency_contact_name' => 'Contact',
                    'emergency_contact_number' => '09111111111',
                ]);

                $currentLeaseStart = $simStart->copy()->addMonths(rand(0, 12));

                for ($contractCount = 0; $contractCount < 3; $contractCount++) {
                    $isFinalContract = ($contractCount === 2);

                    if (($index < 15 || $index >= 25) && $isFinalContract) {
                        $duration = 24;
                        $currentLeaseStart = $today->copy()->subMonths(14);
                    } else {
                        $duration = [6, 12, 24][rand(0, 2)];
                    }

                    $room = $rooms->get(floor($index / 3));
                    $lease = $this->createLease($tenant, $room, $currentLeaseStart, $duration);

                    $currentLeaseStart = Carbon::parse($lease->end_date)->addDay();

                    if (! $isFinalContract && $currentLeaseStart->gt($today)) {
                        break;
                    }
                }
            }

            $currentDate = $simStart->copy();
            while ($currentDate->lte($today)) {
                $this->processMonthlyBillingCycle($currentDate, $today);
                $currentDate->addMonth();
            }
        });

        $this->command->info('🚀 Seeder Complete: Delinquents only owe on ACTIVE contracts.');
    }

    private function createLease($tenant, $room, $start, $months)
    {

        $endDate = $start->copy()->addMonths($months)->subDay();

        $lease = Lease::create([
            'tenant_id' => $tenant->id,
            'room_id' => $room->id,
            'start_date' => $start,
            'end_date' => $endDate,
            'duration_months' => $months,
            'monthly_rent' => rand(4500, 5500),
            // Carbon::now()
            'status' => $endDate->lt(Carbon::create(2026, 2, 12)) ? 'expired' : 'active',
            'pending_electric_debt' => 0,
        ]);

        if ($lease->status === 'active') {
            $room->update(['status' => 'occupied']);
        }

        $this->generateContractualPayments($lease);

        return $lease;
    }

    private function generateContractualPayments($lease)
    {
        $start = Carbon::parse($lease->start_date);
        for ($i = 0; $i < $lease->duration_months; $i++) {
            $dueDate = $start->copy()->addMonths($i);
            $payment = LeasePayment::create([
                'lease_id' => $lease->id,
                'due_date' => $dueDate,
                'amount' => $lease->monthly_rent,
                'status' => 'pending',
                'electric_bill_amount' => 0,
                'carried_over_debt' => 0,
            ]);
            Invoice::create([
                'lease_payment_id' => $payment->id,
                'invoice_number' => 'INV-'.strtoupper(substr(md5(uniqid()), 0, 8)),
                'status' => 'pending',
            ]);
        }
    }

    private function processMonthlyBillingCycle($currentDate, $today)
    {
        $billingStart = $currentDate->copy()->startOfMonth();
        $billingEnd = $currentDate->copy()->endOfMonth();

        $activeRooms = Room::whereHas('leases', function ($q) use ($billingStart, $billingEnd) {
            $q->where('start_date', '<=', $billingEnd)->where('end_date', '>=', $billingStart);
        })->get();

        foreach ($activeRooms as $room) {
            $bill = ElectricBill::create([
                'room_id' => $room->id,
                'billing_month' => $billingStart,
                'total_amount' => rand(2500, 5000),
            ]);
            $this->applyControllerLogic($room, $billingStart, $billingEnd, $bill);
        }
        $this->settlePaymentsForMonth($billingStart, $today);
    }

    protected function applyControllerLogic($room, $billingStart, $billingEnd, $electricBill)
    {
        $leases = Lease::where('room_id', $room->id)
            ->where('start_date', '<=', $billingEnd)
            ->where('end_date', '>=', $billingStart)
            ->get();

        $totalDays = 0;
        foreach ($leases as $l) {
            $s = Carbon::parse($l->start_date)->max($billingStart);
            $e = Carbon::parse($l->end_date)->min($billingEnd);
            $totalDays += $s->diffInDays($e) + 1;
        }

        $costPerDay = $totalDays > 0 ? ($electricBill->total_amount / $totalDays) : 0;

        foreach ($leases as $l) {
            $s = Carbon::parse($l->start_date)->max($billingStart);
            $e = Carbon::parse($l->end_date)->min($billingEnd);
            $days = $s->diffInDays($e) + 1;
            $share = round($costPerDay * $days, 2);

            $payment = LeasePayment::where('lease_id', $l->id)
                ->whereYear('due_date', $billingStart->year)
                ->whereMonth('due_date', $billingStart->month)
                ->first() ?: LeasePayment::where('lease_id', $l->id)->orderBy('due_date', 'desc')->first();

            if (! $payment) {
                continue;
            }

            $isMoveIn = Carbon::parse($l->start_date)->isSameMonth($billingStart);
            if ($isMoveIn || $payment->status === 'paid') {
                $l->increment('pending_electric_debt', $share);
                $payment->update(['electric_bill_id' => $electricBill->id]);
            } else {
                $debt = $l->pending_electric_debt;
                $payment->update([
                    'electric_bill_amount' => $share,
                    'carried_over_debt' => $debt,
                    'electric_bill_id' => $electricBill->id,
                ]);
                $l->update(['pending_electric_debt' => 0]);
            }
        }
    }

    private function settlePaymentsForMonth($month, $today)
    {
        $payments = LeasePayment::whereYear('due_date', $month->year)
            ->whereMonth('due_date', $month->month)
            ->where('due_date', '<', $today)
            ->with(['lease.tenant', 'invoice'])
            ->get();

        foreach ($payments as $p) {
            $tenantEmail = $p->lease->tenant->email;

            preg_match('/\d+/', $tenantEmail, $matches);
            $tenantIndex = isset($matches[0]) ? (int) $matches[0] : 0;

            $isExpired = ($p->lease->status === 'expired');
            $isDelinquentTarget = ($tenantIndex >= 25 && $p->lease->status === 'active');

            if ($isDelinquentTarget) {
                continue;
            }

            $total = $p->amount + $p->electric_bill_amount + $p->carried_over_debt;

            $p->update([
                'status' => 'paid',
                'paid_at' => $p->due_date->copy()->addDays(rand(1, 5)),
            ]);

            if ($p->invoice) {
                $p->invoice->update(['status' => 'paid']);
            }

            $year = $p->paid_at ? $p->paid_at->year : now()->year;
            $nextId = (\App\Models\Receipt::max('id') ?? 0) + 1;
            $receiptNum = 'REC-'.$year.'-'.str_pad($nextId, 5, '0', STR_PAD_LEFT);

            Receipt::create([
                'lease_payment_id' => $p->id,
                'payment_method' => 'cash',
                'amount_paid' => $total,
                'receipt_number' => $receiptNum,
            ]);
        }
    }
}
