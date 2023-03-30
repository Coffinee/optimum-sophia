<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\APIAccess;

class APIAccessController extends BaseController
{

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:sanctum');
    }


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $data = APIAccess::all();

        return $this->sendResponse($data, "All API Access");
    }

    public function getInboundToken()
    {
        $token = APIAccess::where('name', 'inbound-api')->first()->api_token;
        return response()->json(['token' => $token]);
    }

    public function getOutboundToken()
    {
        $token = APIAccess::where('name', 'outbound-api')->first()->api_token;
        return response()->json(['token' => $token]);
    }

    public function getTransactionInboundToken()
    {
        $token = APIAccess::where('name', 'transaction-inbound-api')->first()->api_token;
        return response()->json(['token' => $token]);
    }

    public function getTransactionOutboundToken()
    {
        $token = APIAccess::where('name', 'transaction-outbound-api')->first()->api_token;
        return response()->json(['token' => $token]);
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
        $validated = $request->validate([
            'api_token' => 'required|min:10',
        ]);

        $data = APIAccess::findOrFail($id)->update([
            'api_token' => $request->input('api_token')
        ]);
        return $this->sendResponse($data, "API Access updated");
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
