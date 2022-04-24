<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;
use App\Models\User;
use Tymon\JWTAuth\Facades\JWTAuth;

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

            $userdata = DB::table('users')
            ->where('email',$request->post('email'))
            ->first();
            $userpermission = DB::table('user_permission')
            ->where('user_id',$userdata->id)
            ->get();

            foreach($userpermission as $user_perms){
                if($user_perms->permission_id == 0){
                    array_push($permission,'user');
                }
                if($user_perms->permission_id == 1){
                    array_push($permission,'sender');
                }
                if($user_perms->permission_id == 2){
                    array_push($permission,'admin');
                }
            }

            if($userdata && Hash::check($request->post('password'), $userdata->password)){
                $status = true;

                $credentials = request(['email', 'password']);

                if (!$token = auth()->attempt($credentials)) {
                    return response()->json(['error' => 'Unauthorized'], 401);
                 }
            }else{
                $status = false;
                $userdata = null;
                $userpermission = null;
            }
            return response()->json([
                'status' => $status,
                'userdata' => $userdata,
                'userpermission'=> $permission,
                'token' => $this->respondWithToken($token)
            ]);
        }else{
            return response()->json(['status' => false]);
        }
    }

    public function LoginWithGoogle(Request $request){
        if($request->post('google_uid')){
            $status = false;
            $permission = [];

            $userdata = DB::table('users')
            ->where('google_uid',$request->post('google_uid'))
            ->first();

            if(!empty($userdata)){
                $user = User::where('email', '=', $userdata->email)->first();
                if (!$token = JWTAuth::fromUser($user)) {
                    return response()->json(['error' => 'invalid_credentials'], 401);
                }

                $userpermission = DB::table('user_permission')
                ->where('user_id',$userdata->id)
                ->get();

                foreach($userpermission as $user_perms){
                    if($user_perms->permission_id == 0){
                        array_push($permission,'user');
                    }
                    if($user_perms->permission_id == 1){
                        array_push($permission,'sender');
                    }
                    if($user_perms->permission_id == 2){
                        array_push($permission,'admin');
                    }
                }

                $status = true;

                return response()->json([
                    'status' => $status,
                    'userdata' => $userdata,
                    'userpermission'=> $permission,
                    'token' => $this->respondWithToken($token)
                ]);
            }else{
                return response()->json(['status' => false]);
            }
        }else{
            return response()->json(['status' => false]);
        }
    }

    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60 * 24 * 30,
        ]);
    }

    protected function guard()
    {
        return Auth::guard();
    }
}