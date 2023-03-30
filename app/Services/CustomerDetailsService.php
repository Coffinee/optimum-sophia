<?php

namespace App\Services;

use App\Models\{CustomerDetails, Transaction};
use DB;
use Illuminate\Support\Collection;
use Nette\Utils\Arrays;

class CustomerDetailsService
{
    private $customerDetails, $transactions;

    public function __construct(CustomerDetails $customerDetails, Transaction $transactions)
    {
        $this->customerDetails = $customerDetails;
        $this->transactions = $transactions;
    }

    public function filterByRemitter($keyword)
    {
        $value = $keyword['search'];
        // $keywords = explode(' ', $value);

        // $selectedRemitterId = $customers->first()->id;
        // $beneficiaries = $this->filterBeneByRemitterId($selectedRemitterId);

        // $customer1 = CustomerDetails::where('data_entry_elements', 'like', '%' . $value . '%')
        //     ->orderByDesc('id')
        //     ->toBase()
        //     ->get(['id', 'data_entry_elements']);
        // $queryLog = DB::getQueryLog();

        return $this->filter($value, 'remitter');
    }

    public function filterByBeneficiary($keyword)
    {
        $value = strtoupper($keyword['search']);
        // $keywords = explode(' ', $value);

        // $selectedRemitterId = $customers->first()->id;
        // $beneficiaries = $this->filterBeneByRemitterId($selectedRemitterId);

        // $customer1 = CustomerDetails::where('data_entry_elements', 'like', '%' . $value . '%')
        //     ->orderByDesc('id')
        //     ->toBase()
        //     ->get(['id', 'data_entry_elements']);
        // $queryLog = DB::getQueryLog();

        return $this->filter($value, 'beneficiary');
    }

    public function filter($value, $class): Collection
    {
        return $customers = $this->customerDetails->search($value)
            ->where('class', $class)
            ->get();
    }

    public function filterBeneByRemitterId($id)
    {
        $beneIds = $this->transactions->whereRemitterId($id)->pluck('beneficiary_id');

        return $this->customerDetails
            ->whereIn('id', $beneIds)
            // ->tobase() makes json column into string
            ->get([
                'id', 'class', 'data_entry_elements', 'same_address',
                'same_country',
                'same_state',
                'same_city',
                'same_zip_code',
            ]);
    }
}
