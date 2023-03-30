<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Collection;
use Illuminate\Support\Benchmark;

class Transaction extends Model
{
    use HasFactory, SoftDeletes;

    public const PROCESSING_TYPE_PO1 = 'processing'; //default for batch upload
    public const PROCESSING_TYPE_PO2 = 'amendment';
    public const PROCESSING_TYPE_PO3 = 'refund';

    public const TRANSACTION_STATUS_FOR_APPROVAL = 'for-approval';
    public const TRANSACTION_STATUS_FOR_NOT_APPROVED = 'not-approved';
    public const TRANSACTION_STATUS_FOR_VERIFICATION = 'for-verification';
    public const TRANSACTION_STATUS_FOR_NOT_VERIFIED = 'not-verified';
    public const TRANSACTION_STATUS_APPROVED = 'approved';

    protected $fillable = [
        'company_id',
        'branch_id',
        'user_id',
        'class',
        'user_id',
        'branch_id',
        'company_id',
        'item_count',
        'transaction_date',
        'processing_type',
        //po1-processing,po2-amendment,po3-refund //file-upload-po1-default
        'reference_number',
        'amendment_reference_number', //mandatory for po2 and po3
        'transaction_type',
        //t01-cba,t02-bp,t03-otc,t04-dtd 

        //jdeeremit = bank = bank_name // Transaction_type = OTC, DTD,
        //citiexpress = bank = transaction_type, branch = bank_name, 
        //brunphil = transaction_type = credits to other banks = CBA and BP except GCASH
        //brunphil = transaction_type = OPT-GCASH = gchas payment
        //brunphil = transaction_details = CBA and BP = Bank Name ; Account Number
        //brunphil = transaction_details = Housing Payment = Property Details ; SO Number
        //brunphil = transaction_details = Gcash payment = Gcash ; Account Number
        //redha = transaction_type = CBA(BANK NAMES), BP(SSS,PAIBIG,PHILHEALTH,BP,HOUSEPAYMENT), DTD = CASH, OTC = Cash pickup anywhere.
        //al_ektasad = transaction_type = cba,dtd,otc, CBA - ba

        'original_currency',  //in_currency - List of currencie
        'original_amount', //in_amount
        'exchange_rate',
        'remittance_currency', //OUT_currency - List of currencies
        'gross_amount', //converted_amount
        'service_fee',
        'operation',
        'net_amount', //OUT_amount

        'remitter_id',
        // 'remitter_customer_type', //I-individual, C-corporate

        //beneficiary - cba
        'beneficiary_id',
        'bank_name',
        'bank_branch',
        'account_number',
        'status',

        'instruction_1',
        'instruction_2',
        'instruction_3',
        'good_value_date',

        // 'tieup_id',
        'batch_number',
        // 'meta_name',
        // 'meta_value'


    ];

    public function user()
    {
        return $this->hasOne(User::class, 'id', 'user_id')->select();
    }

    public function remitterName()
    {
        return $this->hasOne(CustomerDetails::class, 'id', 'remitter_id')->select('id', 'data_entry_elements');
    }
    public function beneficiaryName()
    {
        return $this->hasOne(CustomerDetails::class, 'id', 'beneficiary_id')->select('id', 'data_entry_elements');
    }
    public function scopeByStatus($query)
    {

        $role = auth()->user()->role;
        $status = $role == 'maker' || $role == 'verifier' || $role == 'andor' ? 'for-verification' : ($role == 'approver' || $role == 'andor' ? 'for-approval' : '');
        $query->when($status != '', function ($q) use ($status) {
            return $q->where('status', $status);
        });
    }
    public function transactionRemitter()
    {
        return $this->hasOne(CustomerDetails::class, 'id', 'remitter_id')->select('data_entry_elements');
    }

    public function transactionBeneficiary()
    {
        return $this->hasOne(CustomerDetails::class, 'id', 'beneficiary_id')->select('data_entry_elements');

        //'last_name', 'first_name', 'same_address', 'same_city', 'same_state', 'same_country', 'same_zip_code'
    }
    public function currencyName()
    {
        return $this->belongsTo(Country::class, 'original_currency', 'id')->select(['id', 'currency']);
    }
    public function branch()
    {
        return  $this->hasOne(Branch::class, 'id', 'branch_id')->select(['id', 'name', 'logo', 'address', 'city', 'state', 'country', 'region', 'province']);
    }

    public function company()
    {
        return  $this->hasOne(Company::class, 'id', 'company_id')->select(['id', 'name']);
    }

    public function saveDataEntryTransaction($request, $customer_id, $benefeciary_id)
    {
        // dd($request->biller_fields);

        $accountNumber = '';
        $bank_name = '';

        if ($request->transaction_type == "BP") {
            foreach ($request->biller_fields as $fields) {
                if (strtoupper(str_replace(' ', '', $fields['label'])) == 'ACCOUNTNUMBER') {
                    $accountNumber = $fields['value'];
                }
            }
            $bank_name = $request->filled('biller_name') ? $request->biller_name['label'] : "";
        }

        if ($request->transaction_type == "CBA") {
            $bank_name = $request->bank_name['label'];
            $accountNumber = $request->bank_account;
        }

        return $this->create([
            'company_id' => auth()->user()->company_id,
            'branch_id' => auth()->user()->branch_id,
            'user_id' => auth()->user()->id,
            'class' => 'data-entry',
            'transaction_type' => $request->transaction_type,
            'original_currency' => $request->original_currency['label'],
            'original_amount' => $request->original_amount,
            'exchange_rate' => $request->exchange_rate,
            'gross_amount' => $request->gross_amount,
            'service_fee' => $request->service_fee,
            'net_amount' => $request->net_amount,
            'remitter_id' => $customer_id,
            'beneficiary_id' => $benefeciary_id,
            'batch_number' => $request->batch_number,
            'reference_number' => $request->branch_reference_number,
            'bank_name' => $bank_name ?? "",
            'account_number' => $accountNumber ?? '',
            'status' => 'for-verification',
        ]);
    }

    public function getReceiptDetails($id)
    {
        $transaction = $this->findOrFail($id);

        // $remitter_details = json_decode($transaction->transactionRemitter['data_entry_elements']);
        $remitter_details = $transaction->transactionRemitter['data_entry_elements'];

        $JSON_COLUMNS = ['LAST_NAME', 'FIRST_NAME', 'MIDDLE_NAME', 'MOBILE_NUMBER', 'ADDRESS'];

        $remitter = [];

        foreach ($remitter_details as $item) {
            if (in_array(strtoupper(str_replace(' ', '', $item['col_name'])), $JSON_COLUMNS)) {
                // dd($remitter);
                $remitter[] = [
                    $item['col_name'] => $item['value']
                ];
            }
        }

        $remitter = collect($remitter)->collapse();


        if ($transaction->transaction_type != "BP" && $transaction->transaction_type != 'CBA') {

            $beneficiary_details = $transaction->transactionBeneficiary['data_entry_elements'];

            $JSON_COLUMNS = ['LAST_NAME', 'FIRST_NAME', 'MIDDLE_NAME', 'MOBILE_NUMBER', 'ADDRESS', 'CITY', 'COUNTRY', 'STATE', 'ZIP_CODE'];

            $beneficiary = [];

            foreach ($beneficiary_details as $item) {
                if (in_array(strtoupper(str_replace(' ', '', $item['col_name'])), $JSON_COLUMNS)) {
                    // dd($remitter);
                    $beneficiary[] = [
                        $item['col_name'] => $item['value']
                    ];
                }
            }

            $beneficiary = collect($beneficiary)->collapse();
        }

        $data = [
            'type' => $transaction->transaction_type,
            'reference_number' => $transaction->reference_number,
            'date_created' => $transaction->created_at,
            // 'logo' => $transaction->branch->logo,
            // 'address' => $transaction->branch->address,
            // 'company_name' => $transaction->company->name,
            // 'branch_name' => $transaction->branch->name,
            // 'country' => $transaction->branch->countryName->name ?? '',
            // 'state' => $transaction->branch->stateName->name ?? '',
            // 'region' => $transaction->branch->regionName->name ?? '',
            // 'province' =>$transaction->branch->provinceName->name ?? '',
            // 'city' => $transaction->branch->cityName->name ?? '',
            'remitter_name' => $remitter['FIRST_NAME'] . ' ' . $remitter['MIDDLE_NAME'] . ' ' . $remitter['LAST_NAME'],
            'remitter_contact_number' => $remitter['MOBILE_NUMBER'],
            'remitter_address' => $remitter['ADDRESS'],

            'beneficiary_name' => $transaction->transaction_type == "BP" || $transaction->transaction_type == "CBA" ? "" : $beneficiary['LAST_NAME'] . ', ' . $beneficiary['FIRST_NAME'] . ' ' . $beneficiary['MIDDLE_NAME'],
            'currency' => $transaction->original_currency,
            'currency_name' => $transaction->original_currency,
            'equivalent_amount' => $transaction->original_amount,
            'exchange_rate' => $transaction->exchange_rate,
            'gross_amount' => $transaction->gross_amount,
            'service_fee' => $transaction->service_fee,
            'net_amount' => $transaction->net_amount,

            'bank_name' => $transaction->bank_name,
            'bank_account_number' => $transaction->account_number,

            'delivery_address' => $transaction->transaction_type == "BP" || $transaction->transaction_type == "CBA" ? "" : $beneficiary['ADDRESS'] . ', ' . $beneficiary['CITY']
                . ', ' . $beneficiary['STATE'] . ', ' . $beneficiary['COUNTRY'] . ' - ' . $beneficiary['ZIP_CODE'],
        ];

        return $data;
    }


    public static function incrementBatchCount()
    {
        return (float) self::pluck('batch_number')->last() + 1;
    }

    public static function getTransactionStatuses(): array
    {
        return [
            self::TRANSACTION_STATUS_FOR_APPROVAL,
            self::TRANSACTION_STATUS_FOR_VERIFICATION,
            self::TRANSACTION_STATUS_APPROVED,
            self::TRANSACTION_STATUS_FOR_NOT_APPROVED,
            self::TRANSACTION_STATUS_FOR_NOT_VERIFIED,
        ];
    }

    public static function getReadableTransactionStatus(string $TxnStatus)
    {
        $map = [
            self::TRANSACTION_STATUS_FOR_APPROVAL => 'For Approval',
            self::TRANSACTION_STATUS_FOR_VERIFICATION => 'For Verification',
            self::TRANSACTION_STATUS_APPROVED => 'Approved',
        ];

        return $map[$TxnStatus];
    }

    public static function getProcessedTransactions()
    {
        return self::toBase()
            ->whereCompany_id(auth()->user()->company_id)
            ->whereStatus(self::TRANSACTION_STATUS_APPROVED)
            ->get(['batch_number', 'path', 'user_id', 'original_amount', 'gross_amount', 'exchange_rate', 'original_currency', 'status', 'created_at'])
            ->groupBy('batch_number');
    }

    public static function getPendingTransactionsByUserRole($userRole): Collection
    {
        $transactions = collect([]);

        if ($userRole == 'approver' || $userRole == 'andor') {
            $transactions =  self::toBase()
                ->whereCompany_id(auth()->user()->company_id)
                ->whereClass('bulk-upload')
                ->whereStatus(self::TRANSACTION_STATUS_FOR_APPROVAL)
                ->get(['batch_number', 'path', 'user_id', 'original_amount', 'gross_amount', 'exchange_rate',  'status', 'created_at'])
                ->groupBy('batch_number');
        }

        if ($userRole == 'centralized') {
            $transactions =  self::toBase()
                ->whereCompany_id(auth()->user()->company_id)
                ->whereClass('bulk-upload')
                ->whereNot('status', self::TRANSACTION_STATUS_APPROVED)
                ->get(['batch_number', 'path', 'user_id', 'original_amount', 'gross_amount', 'exchange_rate',  'status', 'created_at'])
                ->groupBy('batch_number');
        }

        if ($userRole == 'maker' || $userRole == 'verifier' || $userRole == 'andor') {
            $transactions =  self::toBase()
                ->whereCompany_id(auth()->user()->company_id)
                ->whereClass('bulk-upload')
                ->whereStatus(self::TRANSACTION_STATUS_FOR_VERIFICATION)
                ->get(['batch_number', 'path', 'user_id', 'original_amount', 'gross_amount', 'exchange_rate', 'status', 'created_at'])
                ->groupBy('batch_number');
        }

        return $transactions;
    }

    public static function getAllTransactionsByBatchNumber($batchNumber): Collection
    {
        return self::toBase()
            ->where('batch_number', $batchNumber)
            ->get();
    }

    public static function getCustomerIdsByType($ids, $type)
    {
        return self::select($type)
            ->findMany($ids);
    }
}
