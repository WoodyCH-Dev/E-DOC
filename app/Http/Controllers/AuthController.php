<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class AuthController extends Controller
{
    public function __construct()
    {
        //
    }

    public function Login(Request $request){
        if($request->post('email')){
            $status = false;
            $permission = [];

            $userdata = DB::table('user')
            ->where('email',$request->post('email'))
            ->first();
            $userpermission = DB::table('user_permission')
            ->where('user_id',$userdata->id)
            ->get();

            foreach($userpermission as $user_perms){
                if($user_perms->permission_id == 0){
                    array_push($permission,'user');
                }
                if($user_perms->permission_id == 0){
                    array_push($permission,'sender');
                }
                if($user_perms->permission_id == 0){
                    array_push($permission,'admin');
                }
            }

            if($userdata && Hash::check($request->post('password'), $userdata->password)){
                $status = true;
            }else{
                $status = false;
                $userdata = null;
            }
            return response()->json(['status' => $status,'userdata' => $userdata,'userpermission'=> $permission]);
        }else{
            return response()->json(['status' => false]);
        }
    }
}