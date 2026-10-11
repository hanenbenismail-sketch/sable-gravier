<?php

namespace App\Services;

use App\Models\Expense;
use Illuminate\Support\Facades\DB;
use InvalidArgumentException;

class ExpenseService
{
    public function createExpense(array $data): Expense
    {
        return DB::transaction(function () use ($data) {

            $amount = (float) ($data['amount'] ?? 0);

            if ($amount <= 0) {
                throw new InvalidArgumentException(
                    'Le montant de la dépense doit être supérieur à zéro.'
                );
            }

            if (empty($data['expense_date'])) {
                $data['expense_date'] = now()->toDateString();
            }

            if (empty($data['category'])) {
                throw new InvalidArgumentException(
                    'La catégorie de la dépense est obligatoire.'
                );
            }

            if (empty($data['description'])) {
                throw new InvalidArgumentException(
                    'La description de la dépense est obligatoire.'
                );
            }

            return Expense::create([
                'expense_date' => $data['expense_date'],
                'category' => $data['category'],
                'description' => $data['description'],
                'amount' => $amount,
                'site_id' => $data['site_id'] ?? null,
                'truck_id' => $data['truck_id'] ?? null,
                'driver_id' => $data['driver_id'] ?? null,
                'mission_id' => $data['mission_id'] ?? null,
                'payment_method' => $data['payment_method'] ?? null,
                'reference' => $data['reference'] ?? null,
                'created_by' => $data['created_by'] ?? null,
                'notes' => $data['notes'] ?? null,
            ]);
        });
    }
}