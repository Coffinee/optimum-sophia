<?php

namespace App\Imports;

use App\Models\{Transaction, CustomerDetails};
use Maatwebsite\Excel\Concerns\{ToModel, WithStartRow, SkipsOnError, SkipsErrors, Importable, ToCollection, WithLimit, WithBatchInserts};
use Illuminate\Support\Collection;
use Str;

class TransactionImport implements ToCollection, WithStartRow, WithLimit, WithBatchInserts
{
    /**
     * @param array $row
     *
     * @return \Illuminate\Database\Eloquent\Model|null
     */

    use Importable, SkipsErrors;

    private static int $startingRow = 3;

    private $endRow = 7;

    private string $fileName;

    public function __construct($fileName)
    {
        $this->fileName = $fileName;
    }

    public function collection(Collection $row)
    {

        $dataWithKeys = collect([]);
        $customerDetails = [];
        $timeStamp =  now()->toDateTimeString();
        $batchNumber = Transaction::incrementBatchCount();
        $batchNumber = $batchNumber == 0 ? 1 : $batchNumber;
        $filePath = Str::replace(" ", "_", auth()->user()->user_name) . '/batchUpload/' . now()->isoFormat('YYYYMMDD');
        $fileName = now()->isoFormat('MMDDYYHHmmss') . $batchNumber . '.xls';
        foreach ($row as $item_count => $i) {
            $requiredData = $i->whereNotNull();
            $remitter = null;
            $beneficiary = null;
            if ($requiredData->count() == 6) {

                foreach ($requiredData as $id => $value) {
                    switch ($id) {
                        case (1):
                            $customerDetails[] = [
                                'class' => 'remitter',
                                'last_name' => getLastName($value),
                                'first_name' => getFirstName($value),
                                'middle_name' => $value,
                                'address' => 'null',
                                'created_at' =>  $timeStamp,
                                'updated_at' =>  $timeStamp
                            ];
                            break;

                        case (4):
                            $customerDetails[] = [
                                'class' => 'beneficiary',
                                'last_name' => getLastName($value),
                                'first_name' => getFirstName($value),
                                'middle_name' => $value,
                                'address' => 'null',
                                'created_at' =>  $timeStamp,
                                'updated_at' =>  $timeStamp
                            ];
                            break;

                        case (5):
                            CustomerDetails::insert($customerDetails);
                            $remitter = CustomerDetails::getIdForExcel($customerDetails[0]['class']);
                            $beneficiary = CustomerDetails::getIdForExcel($customerDetails[1]['class']);
                            break;

                            defaut:
                            break;
                    }
                }
                $customerDetails = [];
                $dataWithKeys[] = [
                    'user_id' => auth()->id(),
                    'company_id' => auth()->user()->company_id,
                    'class' => 'bulk-upload',
                    'branch_id' => auth()->user()->branch_id,
                    'class' => 'bulk-upload',
                    'processing_type' => 'PO1',
                    'item_count' => itemCountFormat($item_count),
                    "reference_number" => $requiredData->values()[0],
                    'remitter_id' => $remitter->id,
                    "transaction_type" => $requiredData->values()[2],
                    'beneficiary_id' => $beneficiary->id,
                    // 'beneficiary_address' => 'null',
                    'bank_name' => getBankNameForBrunphil($requiredData->values()[5]),
                    'bank_branch' => getBankNameForBrunphil($requiredData->values()[5]),
                    'account_number' => removeSpecialCharacters(getAccountNumberForBrunphil($requiredData->values()[5])),
                    "gross_amount" => money_format($requiredData->values()[3]),
                    'batch_number' => $batchNumber,
                    'path' => $filePath . '/' . $this->fileName,
                    'status' => Transaction::TRANSACTION_STATUS_FOR_VERIFICATION,
                    'created_at' =>  $timeStamp,
                    'updated_at' =>  $timeStamp
                ];
            }
        }
        return Transaction::insert($dataWithKeys->toArray());
    }

    public function startRow(): int
    {
        return self::$startingRow;
    }

    public function limit(): int
    {
        return $this->endRow;
    }

    public function batchSize(): int
    {
        return 3;
    }
}
