<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Settings\BillersRequest;
use App\Models\Biller;

class BillersController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = Biller::with('billerCategoryName')->paginate(10);
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
        return $this->sendResponse($data, "All billers data");
    }

    public function getBillers(){
        $billers = Biller::select('id','name','fields')->with('serviceFee')->where('company_id', auth()->user()->company_id)->orWhere('company_id', null)->get();
        $arr = [];
        foreach($billers as $i){
            $arr [] = [
                "label" => $i->name,
                "value" => $i->id,
                "fields" => $i->fields,
                "service_fee" => $i->serviceFee->amount,
            ];
        }
        return $this->sendResponse($arr, "All billers in array");
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
    public function store(BillersRequest $request)
    {
        $data = Biller::create([
            'biller_category_id' => $request->input('category.value'),
            'name' => $request->input('name'),
            'company_id' => $request->input('company.value'),
            'branch_id' => $request->input('branch.value'),
            'fields' => collect($request->input('fields'))
        ]);

        return $this->sendResponse($data, "Biller has been added.");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
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
    public function update(BillersRequest $request, $id)
    {
        $data = Biller::findOrFail($id)->update([
            'biller_category_id' => $request->input('category.value'),
            'name' => $request->input('name'),
            'fields' => collect($request->input('fields'))
        ]);
        return $this->sendResponse($data, "Biller has been updated.");
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
}
