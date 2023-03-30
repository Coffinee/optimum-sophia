<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseController;
use App\Http\Requests\CustomerDetails\SearchRequest;
use App\Models\CustomerDetails;
use Illuminate\Http\Request;
use App\Services\CustomerDetailsService;

class CustomerDetailsController extends BaseController
{
    private $customerDetails, $customerDetailsService;

    public function __construct(CustomerDetails $customerDetails, CustomerDetailsService $customerDetailsService)
    {
        $this->customerDetails = $customerDetails;
        $this->customerDetailsService = $customerDetailsService;
    }

    public function search(SearchRequest $request)
    {
        return $this->customerDetailsService->filterByRemitter($request->validated());
    }

    public function searchBeneficiary($id)
    {
        return $this->customerDetailsService->filterBeneByRemitterId($id);
    }
}
