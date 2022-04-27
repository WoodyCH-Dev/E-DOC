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
        return response()->json(['status' => true,'acd_year' => $acd_year->year , 'acd_year_id' => $acd_year->id]);
    }

    public function Get_acd_year_all(Request $request){
        $acd_year_lists = DB::table('year_list')->get();
        return response()->json(['status' => true,'acd_year' => $acd_year_lists]);
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

    public function Sender_Get_AlluserAndGroup(Request $request){
        if($this->ChkUser(1) == false || $this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        $users = DB::table('users')->get();
        $groups = DB::table('user_group')->get();
        return response()->json(['status' => true,'users'=> $users,'groups'=>$groups]);
    }

    public function Get_Allgroup(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        $group =  DB::table('user_group')->get();

        return response()->json(['status' => true,'group' => $group]);
    }

    public function Get_AllDocumentgroup(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        $document_category =  DB::table('document_category')->get();

        return response()->json(['status' => true,'document_category' => $document_category]);
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

    public function AdminAddDocumentGroup(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        DB::table('document_category')
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

    public function AdminEditDocumentGroup(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        DB::table('document_category')
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

    public function AdminRemoveDocumentGroup(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        DB::table('document_category')
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

    public function AdminEditGroupUser(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        $userchk = DB::table('user_ingroup')
        ->where('user_id',$request->post('user_id'))
        ->where('group_id',$request->post('group_id'))
        ->first();

        if($request->post('type') == true){ //Add
            if(!$userchk){
                DB::table('user_ingroup')
                ->insert([
                    'user_id'=> $request->post('user_id'),
                    'group_id'=> $request->post('group_id')
                ]);
            }
        }else if($request->post('type') == false){ //Remove
            if($userchk){
                DB::table('user_ingroup')
                ->where('user_id',$request->post('user_id'))
                ->where('group_id',$request->post('group_id'))
                ->delete();
            }
        }
        return response()->json(['status' => true]);
    }

    public function SenderUploadFiles(Request $request){
        if($this->ChkUser(1) == false || $this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        if ($file = $request->file('file')) {
            $full_filename = "";

            if($request->post('stage')){
                $stage = $request->post('stage');
                $randstr = $this->RandomString(4);
                $name = $file->getClientOriginalName();
                $full_filename = $randstr."_Stage".$stage."_".$name;
                $file->storeAs('uploads/sender',$full_filename,'public');
            }else{
                $randstr = $this->RandomString(4);
                $name = $file->getClientOriginalName();
                $full_filename = $randstr."_".$name;
                $file->storeAs('uploads/sender',$full_filename,'public');
            }

            return response()->json([
                "status" => true,
                "file" => $full_filename
            ]);
        }
    }

    public function SenderSendDocument(Request $request){
        if($this->ChkUser(1) == false || $this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        if($request->post('type') == 'user'){
            $document_id = DB::table('documents')
            ->insertGetId([
                'document_title'=> $request->post('document_title'),
                'document_number'=> $request->post('document_number'),
                'document_category_id'=> $request->post('document_category_id'),
                'document_description'=> $request->post('document_description'),
                'document_stage_id'=> 1,
                'document_priority'=> $request->post('document_priority'),
                'document_status'=> 0,
                'user_id'=> $request->post('user_id'),
                'year_id'=> $request->post('year_id'),
                'timestamp'=> Carbon::now(),
            ]);
            $document_stage = DB::table('document_stage')
            ->insertGetId([
                'stage'=> 1,
                'document_id'=> $document_id,
                'sender_user_id'=> $request->post('user_id'),
                'sender_type'=> 'user',
                'to'=> $request->post('to_id'),
                'status'=> 0,
                'created_timestamp'=> Carbon::now(),
                'read_timestamp'=> null,
            ]);
            foreach($request->post('files') as $file_data){
                $file_upload = DB::table('document_file')->insert([
                    "file" => $file_data['file'],
                    "document_id" => $document_id,
                    "document_stage_id" => $document_stage,
                ]);
            }
            return response()->json(['status' => true]);
        }else if($request->post('type') == 'group'){
            $document_id = DB::table('documents')
            ->insertGetId([
                'document_title'=> $request->post('document_title'),
                'document_number'=> $request->post('document_number'),
                'document_category_id'=> $request->post('document_category_id'),
                'document_description'=> $request->post('document_description'),
                'document_stage_id'=> 1,
                'document_priority'=> $request->post('document_priority'),
                'document_status'=> 0,
                'user_id'=> $request->post('user_id'),
                'year_id'=> $request->post('year_id'),
                'timestamp'=> Carbon::now(),
            ]);
            $document_stage = DB::table('document_stage')
            ->insertGetId([
                'stage'=> 1,
                'document_id'=> $document_id,
                'sender_user_id'=> $request->post('user_id'),
                'sender_type'=> 'group',
                'to'=> $request->post('to_id'),
                'status'=> 0,
                'created_timestamp'=> Carbon::now(),
            ]);
            foreach($request->post('files') as $file_data){
                $file_upload = DB::table('document_file')->insert([
                    "file" => $file_data['file'],
                    "document_id" => $document_id,
                    "document_stage_id" => $document_stage,
                ]);
            }
            return response()->json(['status' => true]);
        }
    }

    public function Sender_Get_MySender(Request $request){
        if($this->ChkUser(1) == false || $this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        $lists = DB::table('documents')
        ->select('*','documents.id as doc_id')
        ->leftJoin('document_category','document_category.id','documents.document_category_id')
        ->where('user_id',$request->post('user_id'))
        ->where('year_id',$request->post('year_id'))
        ->get();
        return response()->json(['status' => true,'lists'=>$lists]);
    }

    public function Admin_Get_AllSender(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        $lists = DB::table('documents')
        ->select('*','documents.id as doc_id')
        ->leftJoin('document_category','document_category.id','documents.document_category_id')
        ->where('year_id',$request->post('year_id'))
        ->get();
        return response()->json(['status' => true,'lists'=>$lists]);
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

    public function RandomString($length) {
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        return $randomString;
    }
}
