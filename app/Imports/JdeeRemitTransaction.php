<?php

namespace App\Imports;

use App\Models\{Transaction, CustomerDetails};
use Maatwebsite\Excel\Concerns\{ToModel, WithStartRow, SkipsErrors, SkipsOnError, Importable, ToCollection, WithLimit, WithChunkReading};
use Illuminate\Support\Collection;
use Str;

class JdeeRemitTransaction implements ToCollection, WithStartRow, SkipsOnError, WithLimit, WithChunkReading
{

    use Importable, SkipsErrors;

    private static int $startingRow = 9;

    public int $endRow = 100;

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

        foreach ($row as $item_count => $i) {
            $requiredData = $i->whereNotNull();
            $remitter = null;
            $beneficiary = null;
            if ($requiredData->count() == 8) {
                foreach ($requiredData as $id => $value) {
                    switch ($id) {
                        case (5):
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
                        case (11):
                            $customerDetails[] = [
                                'class' => 'beneficiary',
                                'last_name' => getLastName($value),
                                'first_name' => getFirstName($value),
                                'middle_name' => $value,
                                'address' => $requiredData->values()[3],
                                'created_at' =>  $timeStamp,
                                'updated_at' =>  $timeStamp
                            ];
                            break;
                        case (27):
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
                    'branch_id' => auth()->user()->branch_id,
                    'class' => 'bulk-upload',
                    'processing_type' => 'PO1',
                    'item_count' => itemCountFormat($item_count),
                    "reference_number" => $requiredData->values()[0],
                    'remitter_id' => $remitter->id,
                    'transaction_type' => strlen($requiredData->values()[4]) <= 3 ? $requiredData->values()[4] : 'CBA',
                    'beneficiary_id' => $beneficiary->id,
                    'bank_name' => $requiredData->values()[4],
                    'bank_branch' => $requiredData->values()[5],
                    'account_number' => removeSpecialCharacters($requiredData->values()[6]),
                    'gross_amount' => $requiredData->values()[7],
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

    public function chunkSize(): int
    {
        return 100;
    }
}
