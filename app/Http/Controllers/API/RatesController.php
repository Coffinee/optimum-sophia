<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Rates;

class RatesController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $is_admin = auth()->user()->role;
        $data = [];
        if($is_admin == 'admin'){
            $data = Rates::with(['fromCurrency', 'toCurrency','fullname', 'branchName'])->paginate(10);
        }else{
            $data = Rates::with(['fromCurrency', 'toCurrency','fullname'])->where('branch_id',auth()->user()->branch_id)->paginate(10);
        }
        
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
        return $this->sendResponse($data, "All Rates data");
    }


    public function getBranchCurrency(){
        $data = Rates::with(['fromCurrency', 'toCurrency'])->where('branch_id', auth()->user()->branch_id)->get();
        $arr = [];

        foreach ($data as $item => $i) {
            $arr[] = [
                "label" => $i->fromCurrency->currency .' - '.$i->toCurrency->currency,
                "value" => $i->fromCurrency->id,
                "rate" => $i->rate
            ];
        }

        return $this->sendResponse($arr, "All currency per branch");
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
    public function store(Request $request)
    {
        //
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
    public function update(Request $request, $id)
    {   
        $data = Rates::findOrFail($id)->update([
            'rate' => $request->input('params.amount'),
            'last_update_by' => auth()->user()->id,
        ]);
        return $this->sendResponse($data, "All Rates data");
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
