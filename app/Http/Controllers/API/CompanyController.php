<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Company\CompanyRequest;
use Illuminate\Http\Request;
use App\Models\Company;
use App\Services\ImageHandlerServices;

class CompanyController extends BaseController
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
   
        $data = Company::paginate(10);
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
        return $this->sendResponse($data, "All companies data");
    }

    public function customSearch(Request $request){
        $search = $request->get('q'); 
        
        $data = Company::where(function($query) use ($search){
            $query->where('name','LIKE',"%$search%")
            ->orWhere('code','LIKE',"%$search%");
        })->paginate(10);

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
        return $this->sendResponse($data, "Company search result");
    }

    public function changeCompanyStatus(Request $request){
        $data = Company::findOrFail($request->input('params.id'));
        $data->is_active = $request->input('params.status') == 1 ? false : true;
        $data->update();
        return $this->sendResponse($data, $request->input('params.status') == 1 ? $data->name.' has been deactivated' : $data->name.' has been activated' );
    }

    public function getCompanies(){
        $data = Company::select(['id','name'])->where('is_active', true)->get();
        $arr = [];
        foreach($data as $i){
            $arr [] = [
                "label" => $i->name,
                "value" => $i->id
            ];
        }
        return $this->sendResponse($arr, "All companies in array");
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
    public function store(CompanyRequest $request, ImageHandlerServices $service)
    {
        $data = Company::create([
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'is_active' => $request->input('is_active'),
            'logo' => $service->saveOriginalImage($request->get('logo')),
        ]);
        return $this->sendResponse($data, "Company created.");
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
        dd($id);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CompanyRequest $request, $id,ImageHandlerServices $service)
    {
        $data = Company::findOrFail($id)->update([
            'name' => $request->input('name'),
            'code' => $request->input('code'),
            'is_active' => $request->input('is_active'),
            'logo' => strlen($request->input('logo')) < 20 ? $request->input('logo') : $service->saveOriginalImage($request->get('logo')),
        ]);
        return $this->sendResponse($data, "Company has been updated.");
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
