<?php

namespace App\Imports;

use App\Models\{CustomerDetails, Transaction};
use Maatwebsite\Excel\Concerns\{ToCollection, ToModel, WithHeadingRow};
use Illuminate\Support\Collection;
use Str;

class CitiExpressTransaction implements ToCollection, WithHeadingRow
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */
    private static $startingRow = 4;

    private string $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    public function collection(Collection $row)
    {
        // dd('citiexpress', $row);
        $dataWithKeys = collect([]);
        $customerDetails = [];
        $timeStamp =  now()->toDateTimeString();
        $batchNumber = Transaction::incrementBatchCount();
        $batchNumber = $batchNumber == 0 ? 1 : $batchNumber;
        $filePath = Str::replace(" ", "_", auth()->user()->user_name) . '/batchUpload/' . now()->isoFormat('YYYYMMDD');

        // $fileName = now()->isoFormat('MMDDYYHHmmss') . $batchNumber . '.xls';
        foreach ($row as $item_count => $i) {
            if ($i->count() == 13) {
                if (!is_null($i['reference_no'])) {
                    $customerDetails[0] = [
                        'class' => 'remitter',
                        "last_name" => $i['remitter_lastname'],
                        "first_name" => $i['remitter_firstname'],
                        'middle_name' => $i['remitter_middlename'],
                        'address' => $i['remitter_address'],
                    ];

                    $customerDetails[1] = [
                        'class' => 'beneficiary',
                        'last_name' => $i['beneficiary_lastname'],
                        'first_name' => $i['beneficiary_firstname'],
                        'middle_name' => $i['beneficiary_middlename'],
                        'address' => $i['beneficiary_address'],
                    ];

                    CustomerDetails::insert($customerDetails);
                    $remitter = CustomerDetails::getIdForExcel($customerDetails[0]['class']);
                    $beneficiary = CustomerDetails::getIdForExcel($customerDetails[1]['class']);

                    $dataWithKeys[] = [
                        'user_id' => auth()->id(),
                        'company_id' => auth()->user()->company_id,
                        'branch_id' => auth()->user()->branch_id,
                        'class' => 'bulk-upload',
                        'processing_type' => 'PO1',
                        'item_count' => itemCountFormat($item_count),
                        "reference_number" => $i['reference_no'],
                        'remitter_id' => $remitter->id,
                        'transaction_type' => strlen($i['bank']) <= 3 ? $i['bank'] : 'CBA',
                        'beneficiary_id' => $beneficiary->id,
                        'bank_name' => $i['bank'],
                        'bank_branch' => $i['branch'],
                        'account_number' => $i['account'],
                        'gross_amount' => money_format($i['amount']),
                        'batch_number' => $batchNumber,
                        'path' => $filePath . '/' . $this->fileName,
                        'status' => Transaction::TRANSACTION_STATUS_FOR_VERIFICATION,
                        'created_at' =>  $timeStamp,
                        'updated_at' =>  $timeStamp
                    ];
                    // dd($dataWithKeys);
                }
                $customerDetails = [];
            }
        }
        return Transaction::insert($dataWithKeys->toArray());
    }

    public function headingRow(): int
    {
        return self::$startingRow;
    }
}
