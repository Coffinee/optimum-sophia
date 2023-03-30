<?php

namespace App\Services;

use App\Models\{Transaction, User};

class TransactionService
{

    public function consolidateByBatchNumberWithPagination($transactions)
    {
        $data = collect([]);
        foreach ($transactions as $transaction) {
            $batchNumber = 0;
            $itemCount = 0;
            $totalAmount = 0;
            $exchangeRate = 0;
            $userId = 0;
            $uploadedAt = null;
            $status = null;

            foreach ($transaction as $value) {
                $amount = $value->original_amount ?: $value->gross_amount;
                $totalAmount += (float)str_replace(',', '', $amount);
                $exchangeRate = $value->exchange_rate;
                $batchNumber = $value->batch_number;
                $original_currency = $value->original_currency;
                $userId = $value->user_id;
                $status = $value->status;
                $uploadedAt = $value->created_at;
            }
            $totalAmount = money_format($totalAmount);
            $itemCount = count($transaction);
            $user = User::findorfail($userId);
            //add company and branch in uploaded_by
            $uploadedBy = strtoupper($user->first_name . ' ' . $user->last_name);

            $data[] = [
                'batch_number' => $batchNumber,
                'filename' => last(explode('/', $value->path)),
                'uploaded_by' => $uploadedBy,
                'email_address' => $user->email,
                'total_count' => $itemCount,
                'total_amount' => $totalAmount,
                'exchange_rate' => $exchangeRate ?? '0.00',
                'original_currency' => $original_currency,
                'date_uploaded' => $uploadedAt,
                'status' => Transaction::getReadableTransactionStatus($status)
            ];
        }
        return paginate($data, 2);
    }
}
