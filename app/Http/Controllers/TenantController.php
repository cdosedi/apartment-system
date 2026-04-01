<?php

namespace App\Http\Controllers;

use App\Http\Requests\UpdateTenantRequest;
use App\Models\Tenant;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class TenantController extends Controller
{
    public function index(Request $request): View
    {
        $search = $request->get('search', '');

        $tenants = Tenant::with(['activeLease.room'])
            ->when($search, fn ($q, $s) => $q->where(fn ($q) => $q->where('tenants.full_name', 'like', "%{$s}%")
                ->orWhere('tenants.email', 'like', "%{$s}%")
                ->orWhere('tenants.contact_number', 'like', "%{$s}%")
            ))
            ->leftJoin('leases as active_leases', function ($join) {
                $join->on('tenants.id', '=', 'active_leases.tenant_id')
                    ->where('active_leases.status', '=', 'active');
            })
            ->leftJoin('rooms', 'active_leases.room_id', '=', 'rooms.id')

            ->orderByRaw('rooms.room_number IS NULL ASC')
            ->orderByRaw('CAST(rooms.room_number AS UNSIGNED) ASC')
            ->orderBy('rooms.room_number', 'ASC')
            ->orderBy('tenants.id')
            ->select('tenants.*')
            ->paginate(15);

        return view('tenants.index', compact('tenants', 'search'));
    }

    public function create(): View
    {
        return view('tenants.create-step1');
    }

    public function leaseForm(Request $request): View
    {

        $tenantData = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'contact_number' => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
            'address' => ['required', 'string', 'max:500'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_number' => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
        ]);

        $tenantData['contact_number'] = preg_replace('/[^0-9]/', '', $tenantData['contact_number']);
        $tenantData['emergency_contact_number'] = preg_replace('/[^0-9]/', '', $tenantData['emergency_contact_number']);

        $availableRooms = \App\Models\Room::available()
            ->orderByRaw('CAST(room_number AS UNSIGNED) ASC')
            ->get();

        return view('tenants.create-step2', compact('tenantData', 'availableRooms'));
    }

    public function agreementPreview(Request $request): View
    {

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'contact_number' => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
            'address' => ['required', 'string', 'max:500'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_number' => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'duration_months' => ['required', 'integer', 'between:1,60'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'monthly_rent' => ['required', 'numeric', 'min:100'],
            'room_id' => ['required', 'exists:rooms,id'],
        ]);

        $data['contact_number'] = preg_replace('/[^0-9]/', '', $data['contact_number']);
        $data['emergency_contact_number'] = preg_replace('/[^0-9]/', '', $data['emergency_contact_number']);

        $room = \App\Models\Room::find($data['room_id']);
        $data['room_number'] = $room->room_number;

        $mockLease = (object) [
            'tenant' => (object) [
                'full_name' => $data['full_name'],
            ],
            'room' => (object) [
                'room_number' => $data['room_number'],
            ],
            'start_date' => \Carbon\Carbon::parse($data['start_date']),
            'end_date' => \Carbon\Carbon::parse($data['end_date']),
            'duration_display' => $this->getDurationDisplay($data['duration_months']),
            'monthly_rent' => $data['monthly_rent'],
            'id' => 'preview',
        ];

        return view('tenants.create-step3', compact('data', 'mockLease'));
    }

    public function finalPreview(Request $request): View
    {

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'contact_number' => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
            'address' => ['required', 'string', 'max:500'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_number' => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'duration_months' => ['required', 'integer', 'between:1,60'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'monthly_rent' => ['required', 'numeric', 'min:100'],
            'room_id' => ['required', 'exists:rooms,id'],
            'agreement_accepted' => ['required', 'accepted'],

        ]);

        $data['contact_number'] = preg_replace('/[^0-9]/', '', $data['contact_number']);
        $data['emergency_contact_number'] = preg_replace('/[^0-9]/', '', $data['emergency_contact_number']);

        $room = \App\Models\Room::find($data['room_id']);
        $data['room_number'] = $room->room_number;

        $tenantData = [
            'full_name' => $data['full_name'],
            'email' => $data['email'],
            'contact_number' => $data['contact_number'],
            'address' => $data['address'],
            'emergency_contact_name' => $data['emergency_contact_name'],
            'emergency_contact_number' => $data['emergency_contact_number'],
        ];

        $leaseData = [
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'],
            'duration_months' => $data['duration_months'],
            'monthly_rent' => $data['monthly_rent'],
            'room_id' => $data['room_id'],
            'room_number' => $data['room_number'],
        ];

        return view('tenants.create-step4', compact('tenantData', 'leaseData', 'data'));
    }

    public function storeWithLease(Request $request): RedirectResponse
    {

        $data = $request->validate([
            'full_name' => ['required', 'string', 'max:255'],
            'email' => ['nullable', 'email', 'max:255'],
            'contact_number' => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
            'address' => ['required', 'string', 'max:500'],
            'emergency_contact_name' => ['required', 'string', 'max:255'],
            'emergency_contact_number' => ['required', 'string', 'regex:/^[0-9]{10,15}$/'],
            'start_date' => ['required', 'date', 'after_or_equal:today'],
            'duration_months' => ['required', 'integer', 'between:1,60'],
            'end_date' => ['required', 'date', 'after:start_date'],
            'monthly_rent' => ['required', 'numeric', 'min:100'],
            'room_id' => ['required', 'exists:rooms,id'],
            'agreement_accepted' => ['required', 'accepted'],
        ]);

        $data['contact_number'] = preg_replace('/[^0-9]/', '', $data['contact_number']);
        $data['emergency_contact_number'] = preg_replace('/[^0-9]/', '', $data['emergency_contact_number']);
        $data['created_by'] = auth()->id() ?? 1;
        $data['status'] = 'active';

        \DB::transaction(function () use ($data) {

            $tenant = \App\Models\Tenant::create([
                'full_name' => $data['full_name'],
                'email' => $data['email'],
                'contact_number' => $data['contact_number'],
                'address' => $data['address'],
                'emergency_contact_name' => $data['emergency_contact_name'],
                'emergency_contact_number' => $data['emergency_contact_number'],
                'created_by' => $data['created_by'],
                'status' => $data['status'],
            ]);

            $lease = $tenant->leases()->create([
                'room_id' => $data['room_id'],
                'start_date' => $data['start_date'],
                'end_date' => $data['end_date'],
                'duration_months' => $data['duration_months'],
                'monthly_rent' => $data['monthly_rent'],
                'status' => 'active',
            ]);

            $this->generateMonthlyDues($lease);

            $this->createdTenantId = $tenant->id;
        });

        $tenant = \App\Models\Tenant::find($this->createdTenantId);

        return redirect()->route('tenants.show', $tenant)
            ->with('success', 'Tenant and lease created successfully. Monthly dues have been generated.');
    }

    private function getDurationDisplay($months)
    {
        $months = (int) $months;

        if ($months < 12) {
            return $months.($months === 1 ? ' Month' : ' Months');
        }

        $years = floor($months / 12);
        $extraMonths = $months % 12;

        $yearText = $years.($years === 1 ? ' Year' : ' Years');
        $monthText = $extraMonths > 0
            ? ' & '.$extraMonths.($extraMonths === 1 ? ' Month' : ' Months')
            : '';

        return $yearText.$monthText;
    }

    protected function generateMonthlyDues($lease): void
    {
        $startDate = \Carbon\Carbon::parse($lease->start_date);

        for ($i = 0; $i < $lease->duration_months; $i++) {
            $dueDate = $startDate->copy()->addMonths($i);

            $coverageStart = $dueDate->copy()->format('M d, Y');
            $coverageEnd = $dueDate->copy()->addMonth()->subDay()->format('M d, Y');

            $isFirstMonth = ($i === 0);

            $payment = \App\Models\LeasePayment::create([
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

    public function show(Tenant $tenant): View
    {

        $leases = \App\Models\Lease::withTrashed()
            ->where('tenant_id', $tenant->id)
            ->with([
                'room',
                'payments' => fn ($q) => $q->orderBy('due_date', 'asc'),
            ])
            ->orderByRaw("CASE WHEN status = 'active' THEN 0 ELSE 1 END")
            ->orderBy('start_date', 'desc')
            ->get();

        $leaseFilters = [];
        foreach ($leases as $lease) {
            $statusLabel = $lease->status === 'active' ? 'Active' : 'Expired';
            $filterKey = "lease_{$lease->id}";
            $label = "{$lease->start_date->format('M d, Y')} → {$lease->end_date->format('M d, Y')} ({$statusLabel})";

            $leaseFilters[$filterKey] = [
                'label' => $label,
                'lease_id' => $lease->id,
                'start_date' => $lease->start_date,
                'end_date' => $lease->end_date,
                'status' => $lease->status,
            ];
        }

        $selectedFilter = request('filter', 'all');

        if ($selectedFilter === 'all') {
            $payments = $leases->flatMap->payments->sortBy('due_date');
        } else {
            $filteredLeaseId = str_replace('lease_', '', $selectedFilter);
            $selectedLease = $leases->firstWhere('id', $filteredLeaseId);
            $payments = $selectedLease ? $selectedLease->payments->sortBy('due_date') : collect();
        }

        if ($selectedFilter === 'all') {
            $filteredLeases = $leases;
        } else {
            $filteredLeaseId = str_replace('lease_', '', $selectedFilter);
            $selectedLease = $leases->firstWhere('id', $filteredLeaseId);
            $filteredLeases = $selectedLease ? collect([$selectedLease]) : collect();
        }

        $tenant->load(['creator']);

        return view('tenants.show', compact(
            'tenant',
            'leases',
            'payments',
            'leaseFilters',
            'selectedFilter',
            'filteredLeases'
        ));
    }

    public function edit(Tenant $tenant): View
    {
        return view('tenants.edit', compact('tenant'));
    }

    public function update(UpdateTenantRequest $request, Tenant $tenant): RedirectResponse
    {
        $tenant->update($request->validated());

        return redirect()->route('tenants.show', $tenant)
            ->with('success', 'Tenant updated successfully.');
    }

    public function destroy(Tenant $tenant): RedirectResponse
    {

        if ($tenant->hasActiveLease()) {
            return back()->withErrors(['active_lease' => 'Cannot deactivate tenant with an active lease.']);
        }

        $tenant->update(['status' => 'inactive']);

        $activeLease = $tenant->leases()->where('status', 'active')->first();
        if ($activeLease && $activeLease->room) {
            $activeLease->room->update(['status' => 'available']);
            $activeLease->update(['status' => 'terminated']);
        }

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant deactivated.');
    }

    public function restore(Tenant $tenant): RedirectResponse
    {
        $tenant->update(['status' => 'active']);

        return back()->with('success', 'Tenant restored.');
    }

    public function delete(Tenant $tenant): RedirectResponse
    {

        if ($tenant->status === 'active') {
            return back()->withErrors(['delete' => 'Cannot delete active tenant. Deactivate first.']);
        }

        if ($tenant->hasActiveLease()) {
            return back()->withErrors(['delete' => 'Cannot delete tenant with active lease history.']);
        }

        $tenant->delete();

        return redirect()->route('tenants.index')
            ->with('success', 'Tenant deleted permanently.');
    }

    public function filterLeasesByLease(Request $request, Tenant $tenant)
    {
        $filter = $request->get('filter', 'all');

        $leases = \App\Models\Lease::withTrashed()
            ->where('tenant_id', $tenant->id)
            ->with(['room'])
            ->orderBy('start_date', 'desc')
            ->get();

        if ($filter === 'all') {
            $filteredLeases = $leases;
        } else {
            $leaseId = str_replace('lease_', '', $filter);
            $selectedLease = $leases->firstWhere('id', $leaseId);
            $filteredLeases = $selectedLease ? collect([$selectedLease]) : collect();
        }

        $leaseFilters = [];
        foreach ($leases as $lease) {
            $statusLabel = $lease->status === 'active' ? 'Active' : 'Expired';
            $filterKey = "lease_{$lease->id}";
            $label = "{$lease->start_date->format('M d, Y')} → {$lease->end_date->format('M d, Y')} ({$statusLabel})";

            $leaseFilters[$filterKey] = [
                'label' => $label,
                'lease_id' => $lease->id,
                'start_date' => $lease->start_date,
                'end_date' => $lease->end_date,
                'status' => $lease->status,
            ];
        }

        return response()->json([
            'html' => view('partials.leases-table-lease', compact('filteredLeases', 'leaseFilters', 'filter', 'leases'))->render(),
        ]);
    }

    public function filterPaymentsByLease(Request $request, Tenant $tenant)
    {
        $filter = $request->get('filter', 'all');

        $leases = \App\Models\Lease::withTrashed()
            ->where('tenant_id', $tenant->id)
            ->with(['payments' => fn ($q) => $q->orderBy('due_date', 'asc')])
            ->orderBy('start_date', 'desc')
            ->get();

        if ($filter === 'all') {
            $payments = $leases->flatMap->payments->sortBy('due_date');
        } else {
            $leaseId = str_replace('lease_', '', $filter);
            $selectedLease = $leases->firstWhere('id', $leaseId);
            $payments = $selectedLease ? $selectedLease->payments->sortBy('due_date') : collect();
        }

        $activeLease = $tenant->leases()->where('status', 'active')->first();
        $unpaidCount = $activeLease ? $activeLease->payments()->whereNotIn('status', ['paid'])->count() : 0;
        $totalAmount = $activeLease ? $activeLease->payments()->whereNotIn('status', ['paid'])->sum('amount') : 0;

        return response()->json([
            'html' => view('partials.payments-table-lease', compact('payments', 'activeLease', 'unpaidCount', 'totalAmount', 'filter', 'leases'))->render(),
        ]);
    }
}
