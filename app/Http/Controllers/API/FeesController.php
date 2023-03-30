<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Biller;
use App\Models\Fees;

class FeesController extends BaseController
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        if(auth()->user()->role != 'admin'){
            $billers = Biller::where('company_id', null)->orWhere('company_id', auth()->user()->company_id)->get(['id']);
            foreach($billers as $item){
                $billerExists = Fees::where(['company_id' => auth()->user()->company_id, 'branch_id' => auth()->user()->branch_id, 'biller_id' => $item->id ])->exists();
                if(!$billerExists){
                    Fees::create([
                        'company_id' => auth()->user()->company_id,
                        'branch_id' => auth()->user()->branch_id,
                        'biller_id' => $item->id,
                        'currency' =>  auth()->user()->currency()->currency,
                        'last_updated_by' => auth()->user()->id,
                    ]);
                }
                
            }
        }
        $data = Fees::with([
          'biller' => function($query) {
            return $query->with(['billerCategoryName' => function($q){
                return $q->with('transactionName');
                } 
            ]);
        }
        , 'fullname'])->where(['company_id' => auth()->user()->company_id, 'branch_id' => auth()->user()->branch_id])->paginate(10);
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
        return $this->sendResponse($data, "All billers");
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
        $data = Fees::findOrFail($id)->update([
            'amount' => $request->input('params.amount'),
            'last_update_by' => auth()->user()->id,
        ]);
        return $this->sendResponse($data, "Fee updated.");
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
