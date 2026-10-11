<?php

namespace App\Services;

use App\Models\Invoice;
use App\Models\Payment;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class PaymentService
{
    public function createPayment(array $data): Payment
    {
        return DB::transaction(function () use ($data) {

            $invoice = Invoice::lockForUpdate()->findOrFail($data['invoice_id']);

            $paidBefore = Payment::where('invoice_id', $invoice->id)
                ->sum('amount');

            $newAmount = (float) $data['amount'];
            $total = (float) $invoice->total;

            if ($newAmount <= 0) {
                throw new InvalidArgumentException(
                    'Le montant du paiement doit être supérieur à zéro.'
                );
            }

            if (($paidBefore + $newAmount) > $total) {
                throw new InvalidArgumentException(
                    'Le paiement dépasse le montant restant de la facture.'
                );
            }

            $payment = Payment::create($data);

            $paidTotal = $paidBefore + $newAmount;
            $remaining = max($total - $paidTotal, 0);

            $paymentStatus = match (true) {
                $paidTotal >= $total => 'paid',
                $paidTotal > 0 => 'partial',
                default => 'unpaid',
            };

            $invoice->update([
                'paid_amount' => $paidTotal,
                'remaining_amount' => $remaining,
                'payment_status' => $paymentStatus,
            ]);

            return $payment;
        });
    }
}
