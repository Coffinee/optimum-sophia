<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\User\UserRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use App\Models\UserPermission;
use App\Services\ImageHandlerServices;

class UserController extends BaseController
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
        $data = User::with(['permissions'])->paginate(10);
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
        return $this->sendResponse($data, "All user data");
    }

    public function checkUserPermission(Request $request)
    {
        $id = auth()->user()->id;
        $ability = $request->get('ability');
        $exists = UserPermission::whereJsonContains('permissions', $ability)->where('user_id', $id)->exists();
        return response()->json(["status" => $exists]);
    }

    public function getAuthenthecatedUser()
    {
        $id = auth()->user()->id;
        $data = User::with('permissions')->where('id', $id)->first();
        return response()->json($data);
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
    public function store(UserRequest $request, ImageHandlerServices $service)
    {

        $data = User::create([
            'role' => $request->input('role.value'),
            'user_name' => $request->input('user_name'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'middle_name' => $request->input('middle_name'),
            'suffix' => $request->input('suffix'),
            'mobile_number' => $request->input('mobile_number'),
            'email' => $request->input('email'),
            'company_id' => $request->input('company.value'),
            'branch_id' => $request->input('branch.value'),
            'status' => $request->input('status') == "1" ? true : false,
            'is_online' => false,
            'profile_picture' => $service->saveOriginalImage($request->input('profile_picture')),
            'password' => Hash::make('asdasd123'),
        ]);

        if ($data) {
            UserPermission::create([
                'user_id' => $data->id,
                'permissions' => collect($request->input('access'))
            ]);
            return $this->sendResponse($data, "User created successfully");
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        dd($id);
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
    public function update(UserRequest $request, $id, ImageHandlerServices $service)
    {
        $data = User::findOrFail($id)->update([
            'role' => $request->input('role.value'),
            'user_name' => $request->input('user_name'),
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'middle_name' => $request->input('middle_name'),
            'suffix' => $request->input('suffix'),
            'mobile_number' => $request->input('mobile_number'),
            'email' => $request->input('email'),
            'company_id' => $request->input('company.value'),
            'branch_id' => $request->input('branch.value'),
            'status' => $request->input('status') == "1" ? true : false,
            'profile_picture' => strlen($request->input('profile_picture')) < 20 ? $request->input('profile_picture') : $service->saveOriginalImage($request->input('profile_picture')),
        ]);

        if ($data) {
            UserPermission::findOrFail($request->input('user_permission_id'))->update([
                'permissions' => collect($request->input('access'))
            ]);
            return $this->sendResponse($data, "User created successfully");
        }
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
