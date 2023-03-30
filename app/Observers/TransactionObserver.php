<?php

namespace App\Observers;
use App\Models\Transaction;
use App\Models\Branch;

class TransactionObserver
{

    public function creating(Transaction $transaction){
        $refNumber = "";
        $existingRef = Transaction::where(['company_id' => auth()->user()->company_id, 'branch_id' => auth()->user()->branch_id])->orderBy('id', 'DESC')->first();

        if($existingRef){
            $concatRef =  substr($existingRef->reference_number,-6);
            $refNumber =  substr($existingRef->reference_number,0,-6).str_pad((int)$concatRef+1, 6, '0', STR_PAD_LEFT);
        }else{
            $refNumber = $transaction->reference_number;
        }
        $transaction->reference_number =  $refNumber;
    }

    public function created(Transaction $transaction){

    }
}
