<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class UserController extends Controller
{
    public function __construct()
    {
        //
    }

    public function Get_acd_year(Request $request){
        $acd_year = DB::table('config')
        ->leftJoin('year_list','year_list.id','config.value')
        ->where('config','year_config')
        ->first();
        return response()->json(['status' => true,'acd_year' => $acd_year->year]);
    }

    public function GetUserCount(Request $request){
        $users =  DB::table('users')->count();
        return response()->json(['status' => true,'count' => $users]);
    }

    public function Get_Alluser(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);

        $users_withgroup = Array();
        $users =  DB::table('users')->get();
        foreach($users as $user){
            $user_ingroup = DB::table('user_ingroup')
            ->leftJoin('user_group','user_group.id','user_ingroup.group_id')
            ->where('user_id',$user->id)
            ->get();

            array_push($users_withgroup,[
                'user'=> $user,
                'user_group'=> $user_ingroup
            ]);
        }
        return response()->json(['status' => true,'users' => $users_withgroup]);
    }

    public function AdminGetUser(Request $request){
        $userdata = DB::table('users')
        ->where('id',$request->route('user_id'))
        ->first();
        return response()->json(['status' => true,'userdata'=> $userdata]);
    }

    public function AdminAddUser(Request $request){
        $insertuser = DB::table('users')
        ->insertGetId([
            'name'=>$request->post('name'),
            'lastname'=>$request->post('lastname'),
            'email'=>$request->post('email'),
            'password'=>Hash::make($request->post('password')),
        ]);
        DB::table('user_permission')
        ->insert([
            'user_id'=> $insertuser,
            'permission_id'=> 0
        ]);
        return response()->json(['status' => true]);
    }

    public function AdminEditUser(Request $request){
        $password = $request->post('password');
        if(!empty($password)){
            DB::table('users')
            ->where('id',$request->post('id'))
            ->update([
                'name' => $request->post('name'),
                'lastname' => $request->post('lastname'),
                'email' => $request->post('email'),
                'password' => Hash::make($password)
            ]);
        }else{
            DB::table('users')
            ->where('id',$request->post('id'))
            ->update([
                'name' => $request->post('name'),
                'lastname' => $request->post('lastname'),
                'email' => $request->post('email'),
            ]);
        }
        return response()->json(['status' => true]);
    }

    public function AdminRemoveUser(Request $request){
        DB::table('users')
        ->where('id',$request->post('id'))
        ->delete();
        return response()->json(['status' => true]);
    }

    public function ChkUser($req_permission){
        $_user = auth()->user();
        if(!$_user){
            return response()->json(['status' => false,'message' => 'User Not found']);
        }else{
            $userpermission = DB::table('user_permission')
            ->where('user_id',$_user->id)
            ->where('permission_id',$req_permission)
            ->first();

            if(empty($userpermission)){
                return false;
            }else{
                return true;
            }
        }
    }
}