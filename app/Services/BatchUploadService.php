<?php

namespace App\Services;

use App\Imports\{TransactionImport, CitiExpressTransaction, JdeeRemitTransaction};
use App\Models\Transaction;
use Maatwebsite\Excel\Facades\Excel;
use Arr;


class BatchUploadService
{
    private $firstLine;

    private $currentLine;

    private static $tieUpRedha = 'OER'; //REDHA

    private static $tieUpBrunphil = 'Brunphil'; //AUE 

    private static $tieUpCitiExpress = 'Citi Express';

    private static $tieUpJdeeRemit = 'Jdee Remit';

    private $excel;

    public function __construct($fileContents, $currentLine = null, $excel = null)
    {
        $this->excel = $excel;
        $this->fileContents = $fileContents;
        $this->firstLine = $fileContents != null ? $fileContents[0] : null;
        $this->currentLine = $currentLine;
    }

    private function getLine($lineNumber, $start, $length)
    {
        return trim(substr($lineNumber, $start, $length));
    }

    public function getCompiledData()
    {
        return  [
            'user_id' => auth()->id(),
            'company_id' => auth()->user()->company_id,
            'branch_id' => auth()->user()->branch_id,
            'item_count' => $this->getItemCount(),
            'transaction_date' =>  $this->gettransactionDate(),
            'processing_type' => 'PO1',
            'reference_number' => $this->getReferenceNumber(),
            'amendment_reference_number' => 'null',
            'transaction_type' => $this->getTransactionType(),
            'original_currency' => $this->getInCurrency(),
            'original_amount' => money_format((float)$this->getInAmount()),
            'exchange_rate' => money_format($this->getExchangeRate()),
            'remittance_currency' => $this->getConvertedCurrency(),
            'gross_amount' => money_format($this->getConvertedAmount()),
            'service_fee' => 'null',
            'net_amount' => money_format($this->getConvertedAmount()),

            'remitter_id' => 'null',

            'remitter_code' => $this->getRemitterCode(),
            'remitter_last_name' => $this->getRemitterLastName(),
            'remitter_first_name' => $this->getRemitterFirstName(),
            'remitter_middle_name' => 'null',
            'remitter_suffix' => 'null',
            'remitter_mobile_number' => 'null',
            'remitter_email' => 'null',

            'remitter_address' => $this->getTieUpValue() ? $this->getRemitterAddress1() : 'null',
            'remitter_country' => $this->getTieUpValue() ? 'null' : $this->getCountry(),
            'remitter_city' => $this->getTieUpValue() ? $this->getRemitterAddress2() : 'null',
            'remitter_state' => $this->getTieUpValue() ? $this->getRemitterAddress3() : 'null',
            'remitter_customer_type' => $this->getTieUpValue() ? $this->getRemitterCustomerType() : 'null',
            'remitter_birth_date' => $this->getTieUpValue() ? $this->getRemitterBirthDate() : 'null',
            'remitter_civil_status' => $this->getTieUpValue() ? $this->getRemitterMaritalStatus() : 'null',

            'remitter_nationality' => $this->getTieUpValue() ? $this->getRemitterNationality() : 'null',
            'remitter_zip_code' => $this->getTieUpValue() ? $this->getZipCode() : 'null',

            'remitter_birth_place' => 'null',
            'remitter_gender' => 'null',

            'remitter_source_of_funds' => 'null',
            'remitter_purpose_of_remittance' => 'null',
            'remitter_relationship_to_the_beneficiary' => 'null',
            'remitter_id_type' => 'null',
            'remitter_id_number' => 'null',
            'remitter_employee_business_nature' => 'null',
            'remitter_employee_business_name' => 'null',
            'remitter_annual_salary' => 'null',

            //beneficiary
            'beneficiary_id' => 'null',

            'beneficiary_code' => $this->getBeneficiaryCode(),
            'beneficiary_last_name' => $this->getBeneficiaryLastName(),
            'beneficiary_first_name' => $this->getBeneficiaryFirstName(),
            'beneficiary_middle_name' => 'null',
            'beneficiary_suffix' => 'null',
            'beneficiary_mobile_number' => 'null',
            'beneficiary_email' => 'null',

            'beneficiary_address' => $this->getTieUpValue() ? $this->getBeneficiaryAddress1() : $this->getAddress1(),
            'beneficiary_country' => $this->getTieUpValue() ? 'null' : $this->getCountry(),
            'beneficiary_city' =>  $this->getTieUpValue() ? $this->getBeneficiaryAddress2() : $this->getCity(),
            'beneficiary_state' =>  $this->getTieUpValue() ? $this->getBeneficiaryAddress3() : $this->getProvince(),
            'beneficiary_customer_type' => $this->getTieUpValue() ? $this->getBeneficiaryCustomerType() : 'null',
            'beneficiary_birth_date' => $this->getTieUpValue() ? $this->getBeneficiaryBirthDate() : 'null',
            'beneficiary_civil_status' => $this->getTieUpValue() ? $this->getBeneficiaryMaritalStatus() : 'null',
            'beneficiary_nationality' => $this->getTieUpValue() ? $this->getBeneficiaryNationality() : 'null',
            'beneficiary_zip_code' => 'null',

            'beneficiary_birth_place' => 'null',
            'beneficiary_gender' => 'null',

            'bank_name' => $this->getBankCode(),
            'bank_branch' => $this->getBankBranch(),
            'account_number' => removeSpecialCharacters($this->getAccountNumber()),
            'instruction_1' => $this->getInstruction1(),
            'instruction_2' => $this->getInstruction2(),
            'instruction_3' => $this->getInstruction3(),
            'good_value_date' => $this->getGoodValueDate(),
        ];
    }

    private function getNameLengthByTieUp()
    {
        return $this->getTieUpValue() ? 100 : 35;
    }

    private function getAddressLengthByTieUp()
    {
        return $this->getTieUpValue() ? 75 : 50;
    }

    private function getTieUpValue()
    {
        // dd($this->getUserTieUp());
        return $this->getUserTieUp() == self::$tieUpRedha;
    }

    private function getUserTieUp()
    {
        return $this->getLine($this->getFileName(), 0, 3);
        // return auth()->user()->companyName->name;
    }

    private function getFileName()
    {
        return $this->getLine($this->firstLine, 0, 13);
    }

    private function getFileDate()
    {
        return $this->getLine($this->firstLine, 13, 10);
    }

    private function getTotalCount()
    {
        return $this->getLine($this->firstLine, 23, 5);
    }

    private function getTotalAmount()
    {
        return $this->getLine($this->firstLine, 28, 14);
    }

    private function getItemCount()
    {
        return $this->getLine($this->currentLine, 0, 5);
    }

    private function getReferenceNumber()
    {
        return $this->getLine($this->currentLine, 5, 22);
    }

    private function getRemitterCode()
    {
        return $this->getLine($this->currentLine, 27, 15);
    }

    private function getRemitterLastName()
    {

        return $this->getLine($this->currentLine, 42, $this->getNameLengthByTieUp());
    }

    private function getRemitterFirstName()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 142 : 71, $this->getNameLengthByTieUp()); //72
    }


    private function getRemitterAddress1()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 242 : 197, 75);
    }

    private function getRemitterAddress2()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 317 : 247, 75);
    }

    private function getRemitterAddress3()
    {
        return $this->getLine($this->currentLine, 392, 50);
    }
    private function getRemitterCustomerType()
    {
        return $this->getLine($this->currentLine, 442, 3);
    }

    private function getRemitterBirthDate()
    {
        return $this->getLine($this->currentLine, 445, 10);
    }

    private function getRemitterMaritalStatus()
    {
        return $this->getLine($this->currentLine, 455, 3);
    }

    private function getRemitterNationality()
    {
        return $this->getLine($this->currentLine, 458, 25);
    }

    private function getBeneficiaryCode()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 483 : 112, 15); // ekta 112
    }

    private function getBeneficiaryLastName()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 498 : 127, $this->getNameLengthByTieUp()); //ekta 127
    }

    private function getBeneficiaryFirstName()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 598 : 162, $this->getNameLengthByTieUp()); //ekta 162
    }

    private function getBeneficiaryAddress1()
    {
        return $this->getLine($this->currentLine, 698, 75);
    }

    private function getBeneficiaryAddress2()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 773 : 197, 50); //ekta 197 , 50 
    }

    private function getBeneficiaryAddress3()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 823 : 247, 50); //ekta 247, 50
    }

    private function getAddress1()
    {
        return $this->getLine($this->currentLine, 197, 50);
    }

    private function getAddress2()
    {
        return $this->getLine($this->currentLine, 247, 50);
    }

    private function getBarangay()
    {
        return $this->getLine($this->currentLine, 297, 3);
    }

    private function getCity()
    {
        return $this->getLine($this->currentLine, 300, 25);
    }

    private function getProvince()
    {
        return $this->getLine($this->currentLine, 325, 25);
    }

    private function getCountry()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 610 : 350, 20);
    }

    private function getBeneficiaryCustomerType()
    {
        return $this->getLine($this->currentLine, 873, 3);
    }

    private function getBeneficiaryBirthDate()
    {
        return $this->getLine($this->currentLine, 876, 10);
    }

    private function getBeneficiaryMaritalStatus()
    {
        return $this->getLine($this->currentLine, 886, 3);
    }

    private function getBeneficiaryNationality()
    {
        return $this->getLine($this->currentLine, 889, 25);
    }

    private function getZipCode()
    {
        return $this->getLine($this->currentLine, 914, 5);
    }

    private function getLandmark()
    {
        return $this->getLine($this->currentLine, 375, 48);
    }

    private function getTelNumber()
    {
        return $this->getLine($this->currentLine, 919, 20);
    }

    private function getInCurrency()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 939 : 444, 5);
    }

    private function getInAmount()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 944 : 450, 14);
    }

    private function getExchangeRate()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 958 : 463, 14);
    }

    private function getConvertedCurrency()
    {

        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 972 : 477, 5);
    }

    private function getConvertedAmount()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 977 : 483, 15);
    }

    private function getMode()
    {
        return $this->getLine($this->currentLine, 496, 10);
    }

    private function getNotify()
    {
        return $this->getLine($this->currentLine, 506, 3);
    }

    private function getTransactionType()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 992 : 509, 4);
    }

    private function getBankCode()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 996 : 513, 50);
    }

    private function getBankBranch()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 1046 : 563, 50);
    }

    private function getAccountNumber()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 1096 : 613, 25);
    }

    private function getTestQuestion()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 1121 : 638, 50);
    }

    private function getTestAnswer()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 1171 : 688, 50);
    }

    private function getInstruction1()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 1221 : 738, 50);
    }

    private function getInstruction2()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 1271 : 788, 50);
    }

    private function getInstruction3()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 1321 : 838, 50);
    }

    private function getTransactionDate()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 1371 : 888, 10);
    }

    private function getGoodValueDate()
    {
        return $this->getLine($this->currentLine, $this->getTieUpValue() ? 1381 : 898, 10);
    }

    public function getImportByFileName($tieup, $permission, $fileName, $exchange_rate = null)
    {
        switch ($tieup) {
            case (self::$tieUpBrunphil):

                $data = $this->compareOrSaveData(new TransactionImport($fileName), $this->excel, $permission);

                $dataWithKeys = collect([]);

                // dd($dataWithKeys);

                if ($permission == '0') {

                    foreach ($data[0] as $i) {
                        // dd($i->whereNotNull());
                        $requiredData = $i->whereNotNull();

                        if ($requiredData->count() == 6) {
                            $dataWithKeys[] = [
                                "reference_number" => $requiredData->values()[0],
                                "remitter_name" => $requiredData->values()[1],
                                "remitter_type" => $requiredData->values()[2],
                                "php_equivalent" => $requiredData->values()[3],
                                "beneficiary_name" => $requiredData->values()[4],
                                "transaction_details" => $requiredData->values()[5] ?? null,
                            ];
                        }
                    }

                    return $this->getDataToCompareFromExcel($dataWithKeys, 'php_equivalent', $exchange_rate);
                } else {

                    return $dataWithKeys;
                }
                break;

            case (self::$tieUpCitiExpress):

                $data = $this->compareOrSaveData(new CitiExpressTransaction($fileName), $this->excel, $permission);
                // dd($data);
                if ($permission == '0') {
                    return $this->getDataToCompareFromExcel($data[0], 'amount',  $exchange_rate);
                } else {
                    return $data;
                }
                break;

            case (self::$tieUpJdeeRemit):

                $data = $this->compareOrSaveData(new JdeeRemitTransaction($fileName), $this->excel, $permission);

                $dataWithKeys = collect([]);
                // dd($data);
                if ($permission == '0') {

                    foreach ($data[0] as $i) {
                        $requiredData = $i->whereNotNull();
                        if ($requiredData->count() == 8) {
                            $dataWithKeys[] = [
                                "reference_number" => $requiredData->values()[0],
                                'remitter_name' => $requiredData->values()[1],
                                'beneficiary_name' => $requiredData->values()[2],
                                'beneficiary_address' => $requiredData->values()[3],
                                'bank' => $requiredData->values()[4],
                                'branch' => $requiredData->values()[5],
                                'account_number' => $requiredData->values()[6],
                                'amount_in_php' => $requiredData->values()[7],
                            ];
                        }
                    }

                    // dd($dataWithKeys->groupBy('bank'));
                    return $this->getDataToCompareFromExcel($dataWithKeys, 'amount_in_php',  $exchange_rate);
                } else {

                    return $dataWithKeys;
                }
                break;
            default:
                break;
        }
    }

    private function getDataToCompareFromExcel($data, $column, $exchange_rate = null)
    {
        $dataToCompare = array(
            'total_count' => number_format($data->count()),
            'total_amount' => money_format($data->sum($column)),
            'exchange_rate' => $exchange_rate,
        );
        // dd($dataToCompare);

        return $dataToCompare;
    }

    private function compareOrSaveData($tieUpImport, $excel, $save_permission)
    {
        if ($save_permission == '1') {
            return Excel::import($tieUpImport, $excel);
        } else {
            return Excel::toCollection($tieUpImport, $excel);
        }
    }
}
