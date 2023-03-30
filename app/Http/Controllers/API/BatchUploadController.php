<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Transaction\BatchUploadRequest;
use App\Models\APIAccess;
use App\Models\CustomerDetails;
use App\Models\Transaction;
use App\Services\BatchUploadService;
use Http;
use Str;
use Arr;
use Storage;

class BatchUploadController extends BaseController
{
    private $url = 'http://127.0.0.1:8000', $exchange_rate = "";
    public function uploadBatchFile(BatchUploadRequest $request)
    {
        static $customer_class_remitter = 'remitter';
        static $customer_class_beneficiary = 'beneficiary';

        if ($request->file('file')) {
            $document = $request->file('file');
            $documentType = getFileExtension($document);
            $documentFileName = getFileName($document);
            $documentFileNameOnly = fileNameOnly_format($documentFileName);

            $file = fopen($request->file('file'), 'r');
            $temp_array = array();
            $data = collect([]);
            //comment this $this->url when using in jes_new_branch or staging
            $this->url = 'http://eos.test';

            $bearerToken = APIAccess::select('api_token')->where('name', 'inbound-api')->first();
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $bearerToken->api_token
            ])->get($this->url . '/api/outbound/cmt-rate', [
                'id' => auth()->user()->company_id,
            ]);

            $cmtRate = $response->json();

            $finalCmtRate = '';

            foreach ($cmtRate['data'] as $rate) {

                // if()
                $finalCmtRate = $rate['activated_rate'] === 'noon' ?  $rate['noon_rate'] : $rate['final_rate'];
            }

            while (!feof($file)) {

                $line = fgets($file);
                // $regex = '/\p{Arabic}+/u';
                // $regex1 = '/[^a-zA-Z0-9\/-]/';

                // if (preg_match('/(?:\p{Arabic}+|[^a-zA-Z0-9\/\-\s.]+)/u', $line) == true) {
                //     $replaced_line = preg_replace('/(?:\p{Arabic}+|[^a-zA-Z0-9\/\-\s.]+)/u', 'kemerut', $line);
                //     // $data->push($replaced_line);
                //     $temp_array[] = $replaced_line;
                // } else {
                //     // $data->push($line);
                //     $temp_array[] = $line;
                // }

                $temp_array[] = $line;
            }

            // dd($temp_array);


            //set exchange rate from cmt hehehe
            $this->exchange_rate = $finalCmtRate;

            if ($documentType == 'txt') {
                foreach ($temp_array as $array) {
                    if ($array != $temp_array[0]) {
                        $convertedReceipt = new BatchUploadService($temp_array, $array);
                        $data->push($convertedReceipt->getCompiledData());
                    }
                }

                $totalCount = $data->count() - 1;
                $totalAmount = 0;

                foreach ($data as $item) {
                    $amount = (float) str_replace(',', '',  $item['original_amount']);
                    $totalAmount += $amount;

                    // $exchangeRate = $item;
                }

                $dataToCompare = array(
                    'total_count' => number_format($totalCount),
                    'total_amount' => money_format($totalAmount),
                    'exchange_rate' => $this->exchange_rate,
                );

                // dd($dataToCompare);

                if ($request->save_permission != '1') {
                    return $this->sendResponse($dataToCompare, "Files needed to be compared");
                }

                $batchNumber = Transaction::incrementBatchCount();
                $batchNumber = $batchNumber == 0 ? 1 : $batchNumber;

                $filePath = Str::replace(" ", "_", auth()->user()->user_name) . '/batchUpload/' . now()->isoFormat('YYYYMMDD');
                $fileName = $documentFileName;

                $remitterKeys = null;
                $beneficiaryKeys = null;
                $compactData = [];
                foreach ($data as $i) {

                    //fetch the remitter details
                    $filteredRemitter = $this->filterCustomerDetails($i, $customer_class_remitter);
                    //creating new array that fits into customer_details table
                    $merge = $this->combineArray($this->getCustomerDetailsKeys(),  $this->getCustomerDetails($filteredRemitter, $customer_class_remitter));
                    //adding the customer_class
                    $remitterDetails = $this->addCustomerClasstoArray($merge);

                    //getting of remitter keys
                    $remitterKeys = array_keys($filteredRemitter);

                    //fetch the beneficiary details
                    $filteredBene = $this->filterCustomerDetails($i, $customer_class_beneficiary);
                    //creating new array that fits into customer_details table
                    $merge = $this->combineArray($this->getCustomerDetailsKeys(),  $this->getCustomerDetails($filteredBene, $customer_class_beneficiary));

                    $beneficiaryDetails = $this->addCustomerClasstoArray($merge);

                    //getting of beneficiary keys
                    $beneficiaryKeys = array_keys($filteredBene);
                    //storing of customer_details

                    CustomerDetails::insert([$remitterDetails, $beneficiaryDetails]);
                    //fetching the lastest customer_details
                    $remitter = CustomerDetails::getId($remitterDetails['code']);
                    $beneficiary = CustomerDetails::getId($beneficiaryDetails['code']);

                    $keys = array_merge($remitterKeys, $beneficiaryKeys);
                    $newArray = Arr::except($i, $keys);

                    // dd($beneficiary->id, $remitter);
                    // dd($remitter->id, $beneficiary);
                    $rowToSave = array_merge($newArray, [
                        'class' => 'bulk-upload',
                        'path' => $filePath . '/' . $fileName,
                        'remitter_id' => $remitter->id ?? null,
                        'beneficiary_id' => $beneficiary->id ?? null,
                        'batch_number' =>  $batchNumber,
                        'status' => Transaction::TRANSACTION_STATUS_FOR_VERIFICATION,
                        'created_at' => now()->toDateTimeString(),
                        'updated_at' => now()->toDateTimeString()
                    ]);
                    $compactData[] = $rowToSave;
                }
                array_pop($compactData);
                // dd($compactData);
                // dd(array_column($compactData, 'remitter_id'));

                $result = Transaction::insert($compactData);


                $request->file('file')->storeAs($filePath, $fileName);


                return $this->sendResponse($result, "Batch File Successfully Saved");
            } else if ($documentType == 'xls' || $documentType == 'xlsx' || $documentType == 'csv') {

                $convertedReceipt = new BatchUploadService(null, null, $document);

                $data = $convertedReceipt->getImportByFileName($documentFileNameOnly, $request->save_permission, $documentFileName, $this->exchange_rate);

                if ($request->save_permission != 1) {
                    return $this->sendResponse($data, "Files needed to be compared");
                }
                return $this->sendResponse($data, "Batch File Successfully Saved");
            }
        }
    }

    private function combineArray($main, $toAdd)
    {
        return array_combine($main, $toAdd);
    }

    private function addCustomerClasstoArray($main)
    {
        $timestamp = now()->toDateTimeString();
        return array_merge($main, ['created_at' => $timestamp, 'updated_at' => $timestamp]);
    }

    private function filterCustomerDetails($dataArray, $customerClass)
    {
        $customerDetails = Arr::where($dataArray, function ($value, $key) use ($customerClass) {
            return str_contains($key, $customerClass);
        });

        return array_merge($customerDetails, ['class' => $customerClass]);
    }

    private function getCustomerDetails($dataArray, $customerType)
    {
        // dd($dataArray, $customerType, $dataArray[$customerType . '_code'],);
        return [
            $customerType,
            $dataArray[$customerType . '_code'],
            $dataArray[$customerType . '_last_name'],
            $dataArray[$customerType . '_first_name'],
            $dataArray[$customerType . '_middle_name'],
            $dataArray[$customerType . '_suffix'],
            $dataArray[$customerType . '_mobile_number'],
            $dataArray[$customerType . '_email'],
            $dataArray[$customerType . '_address'], //address1

            $dataArray[$customerType . '_country'],  //address 2
            $dataArray[$customerType . '_state'],  //address 3
            $dataArray[$customerType . '_city'],  //address 4
            $dataArray[$customerType . '_zip_code'],  //address 5

            $dataArray[$customerType . '_birth_date'],  //remitter birthdate // date of incorporation
            $dataArray[$customerType . '_birth_place'],  //remitter birth place

            $dataArray[$customerType . '_gender'],  //M-male, F-Female

            $dataArray[$customerType . '_civil_status'],
            //marital status S-single,M-maried,W-Widow/er,D-Divorced
            $dataArray[$customerType . '_nationality'],

            $dataArray[$customerType . '_source_of_funds'] ?? null,
            //sf01-salary,sf02-business,sf03-company funds,
            //sf04-loan,sf05-sale of property,sf06-savings or deposits,sf07-others
            $dataArray[$customerType . '_purpose_of_remittance'] ?? null,
            $dataArray[$customerType . '_relationship_to_the_beneficiary'] ?? null,
            $dataArray[$customerType . '_id_type'] ?? null,
            $dataArray[$customerType . '_id_number'] ?? null,
            $dataArray[$customerType . '_employee_business_nature'] ?? null,
            $dataArray[$customerType . '_employee_business_name'] ?? null,
            $dataArray[$customerType . '_annual_salary'] ?? null,
        ];
    }

    private function getCustomerDetailsKeys()
    {
        return  [
            'class',
            'code',
            'last_name',
            'first_name',
            'middle_name',
            'suffix',
            'mobile_number',
            'email',
            'address', //address1

            'country',  //address 2
            'state',  //address 3
            'city',  //address 4
            'zip_code',  //address 5

            'birth_date',  //remitter birthdate // date of incorporation
            'birth_place',  //remitter birth place

            'gender',  //M-male, F-Female

            'civil_status',
            //marital status S-single,M-maried,W-Widow/er,D-Divorced
            'nationality',

            'source_of_funds',
            //sf01-salary,sf02-business,sf03-company funds,
            //sf04-loan,sf05-sale of property,sf06-savings or deposits,sf07-others
            'purpose_of_remittance',
            'relationship_to_the_beneficiary',
            'id_type',
            'id_number',
            'employee_business_nature',
            'employee_business_name',
            'annual_salary',
        ];
    }
}
