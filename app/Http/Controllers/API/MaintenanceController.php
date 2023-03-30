<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Http\Requests\Settings\MaintenanceRequest;
use App\Models\Maintenance;


class MaintenanceController extends BaseController
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $data = Maintenance::with('transactionName')->where('code', $request->get('code'))->paginate(2);
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
        return $this->sendResponse($data, "All source of funds data");
        
    }

    public function getBillerCategories(){
        $data = Maintenance::select(['id','name'])->where('code', 'biller-category')->orderBy('name', 'ASC')->get();
        $arr = [];
        foreach ($data as $i) {
            $arr[] = [
                "label" => $i->name,
                "value" => $i->id
            ];
        }
        return $this->sendResponse($arr, "All biller categories in array");
    }

    public function getTransactionTypes(){
        $data = Maintenance::select(['id','name'])->where('code', 'transaction-type')->orderBy('name', 'ASC')->get();
        $arr = [];
        foreach ($data as $i) {
            $arr[] = [
                "label" => $i->name,
                "value" => $i->id
            ];
        }
        return $this->sendResponse($arr, "All transaction type in array");
    }

    public function getSourceOfFunds(){
        $data = Maintenance::select(['id','name'])->where('code', 'source-of-funds')->orderBy('name', 'ASC')->get();
        $arr = [];
        foreach ($data as $i) {
            $arr[] = [
                "label" => $i->name,
                "value" => $i->id
            ];
        }
        return $this->sendResponse($arr, "All source of funds in array");
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
    public function store(MaintenanceRequest $request)
    {
        $data = Maintenance::create($request->all());

        return $this->sendResponse($data, "Item added.");
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $data = Maintenance::select('id', 'name')->where('code', $id)->get();
        $arr = [];
        foreach($data as $i){
            $arr [] = [
                "label" => $i->name,
                "value" => $i->id
            ];
        }
        return $this->sendResponse($arr, "Single all data");
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
    public function update(MaintenanceRequest $request, $id)
    {
        $data = Maintenance::findOrFail($id)->update([
            'code' => $request->input('code'),
            'ref' => $request->input('ref'),
            'name' => $request->input('name'),
        ]);
        return $this->sendResponse($data, "Item has been updated.");
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $data = Maintenance::findOrFail($id)->delete();
        return $this->sendResponse($data, "Source of fund has been deleted.");
    }
}
