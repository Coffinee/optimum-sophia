<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Branch\BranchRequest;
use App\Models\Branch;
use App\Models\Rates;
use Illuminate\Http\Request;
use App\Services\ImageHandlerServices;

class BranchController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        // $data = Branch::paginate(5);
        $data = Branch::with([
            'company', 'countryName', 'stateName', 'provinceName', 'cityName',
            'rates' => function ($query) {
                return $query->with(['fromCurrency', 'toCurrency']);
            }
        ])->paginate(10);
        $response = [
            'pagination' => [
                'total' => $data->total(),
                'per_page' => $data->perPage(),
                'current_page' => $data->currentPage(),
                'last_page' => $data->lastPage(),
                'from' => $data->firstItem(),
                'to' => $data->lastItem()
            ],
            'data' => $data
        ];

        // dd($data);
        return $this->sendResponse($data, "All Branches data");
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(BranchRequest $request, ImageHandlerServices $service)
    {

        $data = Branch::create([
            'company_id' => $request->input('company.value'),
            'name' => $request->input('name'),
            'address' => $request->input('address'),
            'country' => $request->input('country.value'),
            'state' => $request->input('state.value'),
            'region' => $request->input('region.value'),
            'province' => $request->input('province.value'),
            'city' => $request->input('city.value'),
            'zip_code' => $request->input('zip_code'),
            'prefix' => $request->input('prefix'),
            'logo' => $service->saveOriginalImage($request->get('logo')),
            'status' => $request->input('status'),
            'partner_code' => $request->input('code'),
        ]);

        if ($data->count() > 0) {
            foreach ($request->input('currencies') as $item) {
                Rates::create([
                    'branch_id' => $data->id,
                    'currency_from' => $item['currency_from']['value'],
                    'currency_to' => $item['currency_to']['value'],
                    'last_update_by' => $request->input('user_id'),
                ]);
            }
        }

        return $this->sendResponse($data, "Branch created.");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Branch::select(['id', 'name'])->where('company_id', $id)->get();
        $arr = [];
        foreach ($data as $i) {
            $arr[] = [
                "label" => $i->name,
                "value" => $i->id
            ];
        }
        return $this->sendResponse($arr, "All branches in array");
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Branch $branch, BranchRequest $request)
    {
        $data = $branch->update($request->validated());

        return $this->sendResponse($data, "Branch Updated Successfully");
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

    public function fetchBranchDetails(Branch $branch)
    {
        $data = $branch->with('company')->where('id', $branch->id)->first();
        return $this->sendResponse($data, "branch found");
    }
}
