<?php

namespace App\Http\Controllers;

use App\Http\Requests\PaymentMethodRequest;
use App\Models\Invoice;
use App\Models\LeasePayment;
use App\Models\Receipt;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;

class PaymentController extends Controller
{
    public function create(LeasePayment $payment)
    {
        abort_if($payment->status !== 'pending' && $payment->status !== 'overdue', 403, 'Payment already settled.');

        return view('payments.create', compact('payment'));
    }

    public function store(PaymentMethodRequest $request, LeasePayment $payment): RedirectResponse
    {
        return \DB::transaction(function () use ($request, $payment) {
            $totalToPay = $payment->amount +
                         ($payment->electric_bill_amount ?? 0) +
                         ($payment->carried_over_debt ?? 0);

            $payment->update([
                'status' => 'paid',
                'paid_at' => now(),
            ]);

            $receipt = Receipt::create([
                'lease_payment_id' => $payment->id,
                'payment_method' => $request->payment_method,
                'amount_paid' => $totalToPay,
                'receipt_number' => 'TEMP-'.uniqid(),
            ]);

            $receipt->update([
                'receipt_number' => 'REC-'.now()->year.'-'.str_pad($receipt->id, 5, '0', STR_PAD_LEFT),
            ]);

            return redirect()->to(route('tenants.show', $payment->lease->tenant).'#payments')
                ->with('success', 'Payment of ₱'.number_format($totalToPay, 2).' recorded successfully.');
        });
    }

    public function downloadInvoice(LeasePayment $payment)
    {
        $payment->load(['lease.tenant', 'lease.room', 'electricBill']);

        $invoice = $payment->invoice ?? Invoice::create([
            'lease_payment_id' => $payment->id,
            'invoice_number' => 'INV-'.now()->year.'-'.str_pad(Invoice::count() + 1, 5, '0', STR_PAD_LEFT),
            'status' => $payment->status,
        ]);

        $pdf = Pdf::loadView('pdf.invoice', compact('invoice', 'payment'))
            ->setPaper('a4');

        return $pdf->stream("Invoice_{$invoice->invoice_number}.pdf");
    }

    public function downloadReceipt(LeasePayment $payment)
    {
        $receipt = $payment->receipt;
        abort_if(! $receipt, 404, 'Receipt not found.');

        $receipt->load(['leasePayment.lease.tenant', 'leasePayment.lease.room', 'leasePayment.electricBill']);

        $pdf = Pdf::loadView('pdf.receipt', compact('receipt'))
            ->setPaper('a4');

        return $pdf->stream("Receipt_{$receipt->receipt_number}.pdf");
    }
}
