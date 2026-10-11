<?php

namespace App\Services;

use App\Models\Client;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class InvoiceService
{
    public function createInvoice(
        array $invoiceData,
        array $items
    ): Invoice {
        return DB::transaction(function () use ($invoiceData, $items) {

            if (empty($items)) {
                throw new InvalidArgumentException(
                    'La facture doit contenir au moins un article.'
                );
            }

            $client = Client::findOrFail($invoiceData['client_id']);

            if ($client->status !== 'active') {
                throw new InvalidArgumentException(
                    'Le client n\'est pas actif.'
                );
            }

            $subtotal = 0;
            $discountTotal = 0;
            $taxTotal = 0;

            foreach ($items as $item) {

                if (
                    !isset($item['description']) ||
                    !isset($item['quantity']) ||
                    !isset($item['unit_price'])
                ) {
                    throw new InvalidArgumentException(
                        'Chaque article doit avoir une description, une quantité et un prix unitaire.'
                    );
                }

                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $discount = (float) ($item['discount'] ?? 0);
                $taxRate = (float) ($item['tax_rate'] ?? 0);

                if ($quantity <= 0) {
                    throw new InvalidArgumentException(
                        'La quantité doit être supérieure à zéro.'
                    );
                }

                if ($unitPrice < 0) {
                    throw new InvalidArgumentException(
                        'Le prix unitaire ne peut pas être négatif.'
                    );
                }

                if ($discount < 0) {
                    throw new InvalidArgumentException(
                        'La remise ne peut pas être négative.'
                    );
                }

                if ($taxRate < 0) {
                    throw new InvalidArgumentException(
                        'Le taux de TVA ne peut pas être négatif.'
                    );
                }

                $lineSubtotal = $quantity * $unitPrice;

                $lineDiscount = min(
                    $discount,
                    $lineSubtotal
                );

                $lineAfterDiscount =
                    $lineSubtotal - $lineDiscount;

                $lineTax =
                    $lineAfterDiscount * ($taxRate / 100);

                $lineTotal =
                    $lineAfterDiscount + $lineTax;

                $subtotal += $lineSubtotal;
                $discountTotal += $lineDiscount;
                $taxTotal += $lineTax;
            }

            $total =
                $subtotal
                - $discountTotal
                + $taxTotal;

            $invoice = Invoice::create([
                'invoice_number' => $invoiceData['invoice_number'],
                'client_id' => $client->id,
                'invoice_date' => $invoiceData['invoice_date']
                    ?? now()->toDateString(),
                'due_date' => $invoiceData['due_date'] ?? null,
                'status' => $invoiceData['status'] ?? 'draft',
                'subtotal' => $subtotal,
                'discount' => $discountTotal,
                'tax' => $taxTotal,
                'total' => $total,
                'paid_amount' => 0,
                'remaining_amount' => $total,
                'payment_status' => 'unpaid',
                'notes' => $invoiceData['notes'] ?? null,
            ]);

            foreach ($items as $item) {

                $quantity = (float) $item['quantity'];
                $unitPrice = (float) $item['unit_price'];
                $discount = (float) ($item['discount'] ?? 0);
                $taxRate = (float) ($item['tax_rate'] ?? 0);

                $lineSubtotal = $quantity * $unitPrice;

                $lineDiscount = min(
                    $discount,
                    $lineSubtotal
                );

                $lineAfterDiscount =
                    $lineSubtotal - $lineDiscount;

                $lineTax =
                    $lineAfterDiscount * ($taxRate / 100);

                $lineTotal =
                    $lineAfterDiscount + $lineTax;

                InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'product_id' => $item['product_id'] ?? null,
                    'description' => $item['description'],
                    'quantity' => $quantity,
                    'unit' => $item['unit'] ?? null,
                    'unit_price' => $unitPrice,
                    'discount' => $lineDiscount,
                    'tax_rate' => $taxRate,
                    'subtotal' => $lineSubtotal,
                    'total' => $lineTotal,
                    'mission_id' => $item['mission_id'] ?? null,
                ]);
            }

            return $invoice->fresh([
                'client',
                'items',
            ]);
        });
    }
}