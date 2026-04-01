<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentMethodRequest;
use App\Http\Requests\StoreLeaseRequest;
use App\Models\Lease;
use App\Models\LeasePayment;
use App\Models\Receipt;
use App\Models\Room;
use App\Models\Tenant;
use Barryvdh\DomPDF\Facade\Pdf;
use Carbon\Carbon;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class LeaseController extends Controller
{
    public function index(): View
    {
        $leases = Lease::with(['tenant', 'room'])
            ->where('status', 'active')
            ->orderBy('start_date', 'desc')
            ->paginate(15);

        return view('leases.index', compact('leases'));
    }

    public function downloadAgreement(Lease $lease)
    {
        $lease->load(['tenant', 'room']);

        $pdf = Pdf::loadView('pdf.lease-agreement', compact('lease'))
            ->setPaper('a4')
            ->setOption('margin-top', 10)
            ->setOption('margin-bottom', 10);

        return $pdf->download("Lease_Agreement_Room_{$lease->room->room_number}.pdf");
    }

    public function create(Tenant $tenant): View
    {
        abort_if($tenant->status !== 'active', 403, 'Only active tenants can have leases.');

        $availableRooms = \App\Models\Room::available()
            ->orderByRaw('CAST(room_number AS UNSIGNED) ASC')
            ->get();

        return view('leases.create', compact('tenant', 'availableRooms'));
    }

    public function preview(Request $request, Tenant $tenant): View
    {
        $data = $request->validate([
            'start_date' => 'required|date',
            'duration_months' => 'required|integer|between:1,60',
            'monthly_rent' => 'required|numeric|min:100',
            'room_id' => 'required|exists:rooms,id',
            'end_date' => 'required|date',
        ]);

        $startDate = \Carbon\Carbon::parse($data['start_date']);
        $duration = (int) $data['duration_months'];
        $endDate = $startDate->copy()->addMonths($duration)->subDay();
        $data['end_date'] = $endDate->toDateString();

        $room = \App\Models\Room::find($data['room_id']);

        $tempLease = new \App\Models\Lease($data);

        $mockLease = (object) [
            'tenant' => $tenant,
            'room' => $room,
            'start_date' => $startDate,
            'end_date' => $endDate,
            'monthly_rent' => $data['monthly_rent'],
            'duration_display' => $tempLease->duration_display,
        ];

        return view('leases.create-preview', compact('tenant', 'mockLease', 'data'));
    }

    public function store(StoreLeaseRequest $request, Tenant $tenant): RedirectResponse
    {
        $data = $request->validated();

        $room = Room::findOrFail($data['room_id']);
        if ($room->available_beds <= 0) {
            return back()->withErrors(['room_id' => 'The selected room is not available or has no available beds.']);
        }

        if (empty($data['end_date'])) {
            $data['end_date'] = Carbon::parse($data['start_date'])->addMonths($data['duration_months']);
        }

        \DB::transaction(function () use ($tenant, $data) {
            $lease = $tenant->leases()->create([
                'room_id' => $data['room_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'duration_months' => $data['duration_months'],
                'monthly_rent' => $data['monthly_rent'],
                'status' => 'active',
            ]);

            $this->generateMonthlyDues($lease);
        });

        return redirect()->route('tenants.show', $tenant)
            ->with('success', 'Lease created successfully. Monthly dues have been generated.');
    }

    protected function generateMonthlyDues(Lease $lease): void
    {
        $startDate = Carbon::parse($lease->start_date);

        for ($i = 0; $i < $lease->duration_months; $i++) {
            $dueDate = $startDate->copy()->addMonths($i);

            $coverageStart = $dueDate->copy()->format('M d, Y');
            $coverageEnd = $dueDate->copy()->addMonth()->subDay()->format('M d, Y');

            $isFirstMonth = ($i === 0);

            $payment = LeasePayment::create([
                'lease_id' => $lease->id,
                'due_date' => $dueDate,
                'amount' => $lease->monthly_rent,
                'electric_bill_amount' => 0,
                'carried_over_debt' => 0,
                'electric_bill_id' => null,
                'status' => $isFirstMonth ? 'paid' : 'pending',
                'paid_at' => $isFirstMonth ? now() : null,
                'notes' => "Coverage: $coverageStart - $coverageEnd",
                'is_pro_rated' => false,
            ]);

            if ($isFirstMonth) {
                \App\Models\Receipt::create([
                    'lease_payment_id' => $payment->id,
                    'payment_method' => 'cash',
                    'amount_paid' => $lease->monthly_rent,
                    'receipt_number' => 'REC-'.now()->year.'-'.str_pad((\App\Models\Receipt::max('id') ?? 0) + 1, 5, '0', STR_PAD_LEFT),
                ]);
            }
        }
    }

    public function show(Lease $lease): View
    {
        $lease->load([
            'tenant',
            'room',
            'payments' => fn ($q) => $q->orderBy('due_date'),
        ]);

        return view('leases.show', compact('lease'));
    }

    public function payLeaseInFull(PaymentMethodRequest $request, Lease $lease): RedirectResponse
    {

        $payablePayments = $lease->payments()
            ->where('status', '!=', 'paid')
            ->whereNotNull('electric_bill_id')
            ->get();

        if ($payablePayments->isEmpty()) {
            return back()->with('warning', 'No eligible payments found. Ensure electric bills are issued before paying.');
        }

        \DB::transaction(function () use ($request, $payablePayments) {
            foreach ($payablePayments as $payment) {
                $payment->update([
                    'status' => 'paid',
                    'paid_at' => now(),
                ]);

                $totalAmountPaid = $payment->amount +
                                   $payment->electric_bill_amount +
                                   $payment->carried_over_debt;

                Receipt::create([
                    'lease_payment_id' => $payment->id,
                    'payment_method' => $request->payment_method,
                    'amount_paid' => $totalAmountPaid,
                    'receipt_number' => 'REC-'.now()->year.'-'.str_pad((Receipt::max('id') ?? 0) + 1, 5, '0', STR_PAD_LEFT),
                ]);
            }
        });

        return redirect()->to(route('tenants.show', $lease->tenant).'#payments')
            ->with('success', $payablePayments->count().' payments settled. Receipts generated.');
    }
}
