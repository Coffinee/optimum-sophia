<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\{Transaction, User};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Benchmark;
use Illuminate\Support\Str;
use App\Models\UploadedFiles;
use App\Models\CustomerDetails;
use App\Http\Requests\Transaction\DataEntryBPRequest;
use App\Http\Requests\Transaction\DataEntryOTCRequest;
use App\Http\Requests\Transaction\DataEntryDTDRequest;
use App\Http\Requests\Transaction\DataEntryCBARequest;
use App\Models\CustomFields;
use Illuminate\Support\Facades\Http;
use App\Models\APIAccess;
use App\Models\TransactionHistory;
use App\Services\TransactionService;

class TransactionController extends BaseController
{
    private $customer, $transaction, $customfields, $transactionService, $url = 'http://127.0.0.1:8000';

    public function __construct(CustomerDetails $customer, Transaction $transaction, CustomFields $customfields, TransactionService $transactionService)
    {
        $this->middleware('auth:sanctum');
        $this->customer = $customer;
        $this->transaction = $transaction;
        $this->customfields = $customfields;
        $this->transactionService = $transactionService;
        //comment this $this->url when using in jes_new_branch or staging
        $this->url = 'http://eos.test';
    }

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
    }

    public function uploadFile(Request $request)
    {
        $uuid = (string) Str::uuid();
        $imageName = substr($uuid, 0, 6) . '_' . time() . '.' . $request->selectedFiles->getClientOriginalExtension();
        $request->selectedFiles->move(public_path('/uploads/files'), $imageName);
        $data = UploadedFiles::create([
            'batch_code' => $request->batch_number,
            'file_name' => $imageName,
            'extension' => $request->selectedFiles->getClientOriginalExtension(),
        ]);
        return $this->sendResponse($data, "All request");
    }

    public function validateBPFields(DataEntryBPRequest $request)
    {
        $validatedData = $request->validated();
        if ($validatedData) {
            return $this->sendResponse($validatedData, "All request");
        }
    }

    public function validateOTCFields(DataEntryOTCRequest $request)
    {
        $validatedData = $request->validated();
        if ($validatedData) {
            return $this->sendResponse($validatedData, "All request");
        }
    }

    public function validateDTDFields(DataEntryDTDRequest $request)
    {
        $validatedData = $request->validated();
        if ($validatedData) {
            return $this->sendResponse($validatedData, "All request");
        }
    }

    public function saveBPTransaction(DataEntryBPRequest $request)
    { //get remitter_details and store it in customer_details
        DB::beginTransaction();

        try {
            $customer = $this->customer->saveRemitter($request);

            if ($customer->exists) {

                $transaction = $this->transaction->saveDataEntryTransaction($request, $customer->id, '');

                if ($transaction->exists) {

                    $this->customfields->saveSourceOfFundFields($request, $transaction);

                    $this->customfields->saveBillerFields($request, $transaction);
                }
                $response = [
                    'transaction_id' => $transaction->id,
                ];

                DB::commit();
                return $this->sendResponse($response, "Transaction completed.");
            }
        } catch (\Exception $e) {
            DB::rollback();
            return $e;
            // Handle the exception
        }
    }

    public function saveOTCTransaction(DataEntryOTCRequest $request)
    {
        DB::beginTransaction();

        try {

            $customer = $this->customer->saveRemitter($request);
            $beneficiary = $this->customer->saveBeneficiary($request);

            if ($customer->exists && $beneficiary->exists) {

                $transaction = $this->transaction->saveDataEntryTransaction($request, $customer->id, $beneficiary->id);
                if ($transaction->exists) {
                    $this->customfields->saveSourceOfFundFields($request, $transaction);
                }
                $response = [
                    'transaction_id' => $transaction->id,
                ];
                DB::commit();
                return $this->sendResponse($response, "Transaction completed.");
            }
        } catch (\Exception $e) {
            return $e;
            DB::rollback();
            // Handle the exception
        }
    }

    public function saveDTDTransaction(DataEntryDTDRequest $request)
    {

        DB::beginTransaction();
        try {
            $customer = $this->customer->saveRemitter($request);

            $beneficiary = $this->customer->saveBeneficiary($request);

            if ($customer->exists && $beneficiary->exists) {
                $transaction = $this->transaction->saveDataEntryTransaction($request, $customer->id, $beneficiary->id);
                if ($transaction->exists) {
                    $this->customfields->saveSourceOfFundFields($request, $transaction);
                }
                $response = [
                    'transaction_id' => $transaction->id,
                ];
                DB::commit();
                return $this->sendResponse($response, "Transaction completed.");
            }
        } catch (\Exception $e) {

            DB::rollback();
            // Handle the exception
        }
    }

    public function saveCBATransaction(DataEntryCBARequest $request)
    {
        DB::beginTransaction();

        try {
            //must check first if the customer is existing or not to know if update or store a new one. 
            //should or should not allow to save if the custommer name exists but has different details
            //the unique identifier must be the customer_details.id
            $customer = $this->customer->saveRemitter($request);

            // $beneficiary = $this->customer->saveBeneficiary($request);

            if ($customer->exists) {
                $transaction = $this->transaction->saveDataEntryTransaction($request, $customer->id, null);

                if ($transaction->exists) {
                    $this->customfields->saveSourceOfFundFields($request, $transaction);
                }
                $response = [
                    'transaction_id' => $transaction->id,
                ];
                DB::commit();
                return $this->sendResponse($response, "Transaction completed.");
            }
        } catch (\Exception $e) {
            DB::rollback();
            // Handle the exception
        }
    }


    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(DataEntryBPRequest $request)
    {

        // $customer = $this->customer->saveRemitter($request);
        // if($customer->exists){
        //     $transaction = $this->transaction->saveDataEntryTransaction($request, $customer);
        // if($transaction->exists){
        //         $this->customfields->saveSourceOfFundFields($request, $transaction);
        //         $this->customfields->saveBillerFields($request, $transaction);
        // }
        // $response = [
        //         'transaction_id' => $transaction->id,
        // ];
        // return $this->sendResponse($response, "Transaction completed.");
        // }
    }



    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = $this->transaction->getReceiptDetails($id);

        return $this->sendResponse($data, "Transaction completed.");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        if ($request->input('status') == 'approved') {
            $bearerToken = APIAccess::select('api_token')->where('name', 'transaction-outbound-api')->first();
            $transactionData = $this->transaction->find($id)->makeHidden('deleted_at');
            $remitterData = $this->customer->find($transactionData->remitter_id);
            $beneficiaryData = $this->customer->find($transactionData->beneficiary_id);
            $user = User::findOrFail($transactionData->user_id);
            // dd($user, $transactionData->user_id);
            $transactionData = array_merge(
                $transactionData->toArray(),
                [
                    'uploader_id' => $user->id,
                    'uploaded_by' => $user->last_name . ' ' . $user->first_name . ', ' . $user->middle_name,
                    'uploader_email' => $user->email,
                ]
            );

            DB::beginTransaction();
            try {
                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $bearerToken->api_token
                ])->post($this->url . '/api/transaction-inbound/transaction', [
                    'transaction' => $transactionData,
                    'remitter' => $remitterData,
                    'beneficiary' => $beneficiaryData
                ]);

                // dd($response->body());

                $data = Transaction::findOrFail($id)->update([
                    'status' => $request->input('status')
                ]);
                $history = TransactionHistory::create([
                    'transaction_id' => $id,
                    'user_id' => auth()->user()->id,
                    'status' => $request->input('status'),
                ]);
                // dd($response->body());
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } else {
            $history = TransactionHistory::create([
                'transaction_id' => $id,
                'user_id' => auth()->user()->id,
                'status' => $request->input('status'),
                'remarks' => $request->filled('remarks') ? $request->input('remarks') : "",
            ]);
            $data = Transaction::findOrFail($id)->update([
                'status' => $request->input('status')
            ]);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function declineBatch(Request $request)
    {
        DB::beginTransaction();
        try {
            $ids = $this->transaction->where('batch_number', $request->batchNumber)->pluck('id');

            $transactionHistoryData = $ids->map(function ($id) use ($request) {
                return [
                    'transaction_id' => $id,
                    'user_id' => auth()->user()->id,
                    'status' => 'declined',
                    'remarks' => $request->remarks,
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            });

            TransactionHistory::insert($transactionHistoryData->all());

            return $this->sendResponse(
                $this->transaction->where('batch_number', $request->batchNumber)->delete(),
                'Batch Number has been declined'
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
    }

    public function bulkDeclineBatch(Request $request)
    {
        return $this->sendResponse(
            $this->transaction->whereIn('batch_number', $request->selectedBatchNumber)->delete(),
            'Batch Number has been declined'
        );
    }

    public function allProcessedTransactions()
    {

        // $bearerToken = APIAccess::select('api_token')->where('name', 'transaction-bound-api')->first();
        // $response = Http::withHeaders([
        //     'Authorization' => 'Bearer ' . $bearerToken->api_token
        // ])->get($this->url . '/api/transaction-outbound/processed');

        // dd($response->body(), $bearerToken);

        $transactions = $this->transaction->getProcessedTransactions();
        //

        return $this->sendResponse($this->transactionService->consolidateByBatchNumberWithPagination($transactions), "All Processed Transactions By Pagination");
    }

    public function allMonitorTransaction()
    {
        $data = Transaction::with(['remitterName', 'beneficiaryName', 'currencyName'])->byStatus()->where('class', 'data-entry')->paginate(10);
        return $this->sendResponse($data, "All Monitor Transactions By Pagination");
    }

    public function trackMakersMonitorTransactions()
    {
        $data = Transaction::with(['remitterName', 'beneficiaryName', 'currencyName'])
            // ->byStatus()
            ->where('class', 'data-entry')
            ->where('user_id', auth()->user()->id)
            ->paginate(10);

        // dd($data);

        return $this->sendResponse($data, "All Monitor Transactions By Pagination");
    }

    public function allPendingTransactions()
    {
        //batch number, filename, uploaded_by, email-address,
        //item_count, total_amount, exchange_rate, date_uploaded.

        $transactions = $this->transaction->getPendingTransactionsByUserRole(auth()->user()->role);

        // return $this->sendResponse($this->transactionService->consolidateByBatchNumberWithPagination($transactions), "All Pending Transactions By Pagination");
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
                $totalAmount += (float) str_replace(',', '', $amount);
                $exchangeRate = $value->exchange_rate;
                $batchNumber = $value->batch_number;
                $userId = $value->user_id;
                $status = $value->status;
                $uploadedAt = $value->created_at;
            }
            $totalAmount = money_format($totalAmount);
            $itemCount = count($transaction);
            $user = User::findorfail($userId);
            $uploadedBy = strtoupper($user->company_id) . ' - ' . $user->first_name . ' ' . $user->last_name;

            $data[] = [
                'batch_number' => $batchNumber,
                'filename' => last(explode('/', $value->path)),
                'uploaded_by' => $uploadedBy,
                'email_address' => $user->email,
                'total_count' => $itemCount,
                'total_amount' => $totalAmount,
                'exchange_rate' => $exchangeRate ?? '0.00',
                'date_uploaded' => $uploadedAt,
                'status' => Transaction::getReadableTransactionStatus($status)
            ];
        }
        $data = paginate($data, 2);
        return $this->sendResponse($data, "All Pending Transactions By Pagination");
    }

    public function trackMakersPendingTransactions()
    {
        $user = auth()->user();
        // $transactions = $this->transaction->getPendingTransactionsByUserRole($user->role)->where('user_id', $user->id);
        $transactions = $this->transaction->toBase()
            ->whereCompany_id(auth()->user()->company_id)
            ->whereClass('bulk-upload')
            ->get(['batch_number', 'path', 'user_id', 'original_amount', 'gross_amount', 'exchange_rate',  'status', 'created_at'])
            ->groupBy('batch_number');

        // return $this->sendResponse($this->transactionService->consolidateByBatchNumberWithPagination($transactions), "All Pending Transactions By Pagination");
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
                $totalAmount += (float) str_replace(',', '', $amount);
                $exchangeRate = $value->exchange_rate;
                $batchNumber = $value->batch_number;
                $userId = $value->user_id;
                $status = $value->status;
                $uploadedAt = $value->created_at;
            }
            $totalAmount = money_format($totalAmount);
            $itemCount = count($transaction);
            $user = User::findorfail($userId);
            $uploadedBy = strtoupper($user->company_id) . ' - ' . $user->first_name . ' asdsa' . $user->last_name;

            $data[] = [
                'batch_number' => $batchNumber,
                'filename' => last(explode('/', $value->path)),
                'uploaded_by' => $uploadedBy,
                'email_address' => $user->email,
                'total_count' => $itemCount,
                'total_amount' => $totalAmount,
                'exchange_rate' => $exchangeRate ?? '0.00',
                'date_uploaded' => $uploadedAt,
                'status' => Transaction::getReadableTransactionStatus($status)
            ];
        }
        $data = paginate($data, 10);

        return $this->sendResponse($data, "All Pending Transactions By Pagination");
    }

    public function bulkChangeStatusByRole(Request $request)
    {
        $batchNumbers = $request->selectedBatchNumber;
        $userRole = auth()->user()->role;

        if ($userRole == 'verifier' || $userRole == 'andor') {
            $this->bulkUpdateStatus($batchNumbers, Transaction::TRANSACTION_STATUS_FOR_APPROVAL);
        }

        if ($userRole == 'approver' || $userRole == 'centralized') {
            $this->bulkUpdateStatus($batchNumbers, Transaction::TRANSACTION_STATUS_APPROVED);

            //send email after approval
        }
    }

    public function changeStatusByRole(Request $request)
    {
        $batchNumber = $request->batchNumber;

        $userRole = auth()->user()->role;

        if ($userRole == 'verifier' || $userRole == 'andor') {
            $this->updateStatus($batchNumber, Transaction::TRANSACTION_STATUS_FOR_APPROVAL);
        }

        if ($userRole == 'approver' || $userRole == 'centralized') {
            $this->updateStatus($batchNumber, Transaction::TRANSACTION_STATUS_APPROVED);

            //send email after approval
        }
    }

    private function updateStatus($batchNumber, $status)
    {
        $ids = $this->transaction->where('batch_number', $batchNumber)->pluck('id');
        $bearerToken = APIAccess::select('api_token')->where('name', 'transaction-outbound-api')->first();
        $transactionData = $this->transaction->findMany($ids)->makeHidden('deleted_at');
        $remitter_ids = $this->transaction->getCustomerIdsByType($ids, 'remitter_id');
        $beneficiary_ids = $this->transaction->getCustomerIdsByType($ids, 'beneficiary_id');

        $remitterData = $this->customer->find($remitter_ids);
        $beneficiaryData = $this->customer->find($beneficiary_ids);
        $user = User::findOrFail($transactionData->first()->user_id);

        $transactionData = $transactionData->map(function ($transaction) use ($user) {
            return array_merge(
                $transaction->toArray(),
                [
                    'uploader_id' => $user->id,
                    'uploaded_by' => $user->last_name . ' ' . $user->first_name . ', ' . $user->middle_name,
                    'uploader_email' => $user->email,
                ]
            );
        });

        if ($status == 'approved') {

            DB::beginTransaction();
            try {

                $response = Http::withHeaders([
                    'Authorization' => 'Bearer ' . $bearerToken->api_token
                ])->post($this->url . '/api/transaction-inbound/transaction', [
                    'transaction' => $transactionData,
                    'remitter' => $remitterData,
                    'beneficiary' => $beneficiaryData,
                ]);


                $data = $this->transaction->where('batch_number', $batchNumber)
                    ->update([
                        'status' => $status
                    ]);

                $history = $ids->map(function ($id) use ($status) {
                    return [
                        'transaction_id' => $id,
                        'user_id' => auth()->user()->id,
                        'status' => $status,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                });

                TransactionHistory::insert($history->all());
                // dd($response->body());

                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        } else {
            DB::beginTransaction();
            try {
                $history = $ids->map(function ($id) use ($status) {
                    return [
                        'transaction_id' => $id,
                        'user_id' => auth()->user()->id,
                        'status' => $status,
                        'remarks' => "",
                        'created_at' => now(),
                        'updated_at' => now(),
                    ];
                });

                TransactionHistory::insert($history->all());

                $data = $this->transaction->where('batch_number', $batchNumber)
                    ->update([
                        'status' => $status
                    ]);
                DB::commit();
            } catch (\Exception $e) {
                DB::rollback();
                throw $e;
            }
        }
    }

    private function bulkUpdateStatus($batchNumbers, $status)
    {
        $data = $this->transaction->whereIn('batch_number', $batchNumbers)
            ->update([
                'status' => $status
            ]);

        return $this->sendResponse($data, "All Pending Transactions By Pagination");
    }
}
