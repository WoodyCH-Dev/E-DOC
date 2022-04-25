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

    public function Get_Allgroup(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        $group =  DB::table('user_group')->get();

        return response()->json(['status' => true,'group' => $group]);
    }

    public function SyncWithGoogle(Request $request){
        DB::table('users')
        ->where('id',$request->post('id'))
        ->update([
            'google_uid' => $request->post('google_uid'),
        ]);
        return response()->json(['status' => true]);
    }

    public function UnSyncWithGoogle(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        DB::table('users')
        ->where('id',$request->post('id'))
        ->update([
            'google_uid' => null,
        ]);
        return response()->json(['status' => true]);
    }

    public function AdminGetUser(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        $userdata = DB::table('users')
        ->where('id',$request->route('user_id'))
        ->first();
        return response()->json(['status' => true,'userdata'=> $userdata]);
    }

    public function AdminAddUser(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
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

    public function AdminAddGroup(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        DB::table('user_group')
        ->insertGetId([
            'group_name'=>$request->post('group_name'),
        ]);
        return response()->json(['status' => true]);
    }

    public function AdminEditUser(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
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

    public function AdminEditGroup(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        DB::table('user_group')
        ->where('id',$request->post('id'))
        ->update([
            'group_name'=>$request->post('group_name'),
        ]);
        return response()->json(['status' => true]);
    }

    public function AdminRemoveUser(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        DB::table('users')
        ->where('id',$request->post('id'))
        ->delete();
        return response()->json(['status' => true]);
    }

    public function AdminRemoveGroup(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        DB::table('user_group')
        ->where('id',$request->post('id'))
        ->delete();
        return response()->json(['status' => true]);
    }

    public function AdminGetUserPermission(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        $userpermission = DB::table('user_permission')
        ->where('user_id',$request->post('user_id'))
        ->get();
        $user = false;
        $sender = false;
        $admin = false;
        foreach($userpermission as $user_perms){
            if($user_perms->permission_id == 0){
                $user = true;
            }
            if($user_perms->permission_id == 1){
                $sender = true;
            }
            if($user_perms->permission_id == 2){
                $admin = true;
            }
        }
        return response()->json([
            'status' => true,
            'permission_user' => $user,
            'permission_sender' => $sender,
            'permission_admin' => $admin
        ]);
    }

    public function AdminEditUserPermission(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        $userpermissionchk = DB::table('user_permission')
        ->where('user_id',$request->post('user_id'))
        ->where('permission_id',$request->post('permission_id'))
        ->first();

        if($request->post('type') == true){ //Add
            if(!$userpermissionchk){
                DB::table('user_permission')
                ->insert([
                    'user_id'=> $request->post('user_id'),
                    'permission_id'=> $request->post('permission_id')
                ]);
            }
        }else if($request->post('type') == false){ //Remove
            if($userpermissionchk){
                DB::table('user_permission')
                ->where('user_id',$request->post('user_id'))
                ->where('permission_id',$request->post('permission_id'))
                ->delete();
            }
        }
        return response()->json(['status' => true]);
    }

    public function Get_Alluser_InGroup(Request $request){
        $usersingroup =  DB::table('user_ingroup')
        ->leftJoin('users','users.id','user_ingroup.user_id')
        ->where('group_id',$request->route('group_id'))
        ->get();
        return response()->json(['status' => true, 'users'=>$usersingroup]);
    }

    //Chk User Function
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