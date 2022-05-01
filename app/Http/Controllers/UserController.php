<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
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

    public function GetDocumentCount(Request $request){
        $doc_count =  DB::table('documents')->where('year_id',$request->route('year_id'))->count();
        return response()->json(['status' => true,'count' => $doc_count]);
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

    public function GetMyInbox(Request $request){
        $_user = auth()->user();
        $mygroup = DB::table('user_ingroup')
        ->leftJoin('user_group','user_group.id','user_ingroup.group_id')
        ->where('user_id',$_user->id)
        ->get();

        $sendto_userid = DB::table('document_stage')
        ->select('*','document_stage.id as stage_id')
        ->leftJoin('documents','documents.id','document_stage.document_id')
        ->leftJoin('users','users.id','document_stage.sender_user_id')
        ->where('sender_type','user')
        ->where('documents.document_status',0)
        ->where('to',$_user->id)
        ->get();
        $sendto_group = Array();
        foreach($mygroup as $group){
            $group_chk = DB::table('document_stage')
            ->leftJoin('documents','documents.id','document_stage.document_id')
            ->leftJoin('users','users.id','document_stage.sender_user_id')
            ->where('sender_type','group')
            ->where('to',$group->group_id)
            ->where('documents.document_status',0)
            ->get();

            if($group_chk->count() > 0){
                array_push($sendto_group,$group_chk);
            }
        }
        return response()->json(['status' => true,'sendto_user'=>$sendto_userid,'sendto_group'=>$sendto_group]);
    }

    public function Sender_Get_AlluserAndGroup(Request $request){
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
        if($this->ChkUser(1) == false && $this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);

            $document_id = DB::table('documents')
            ->insertGetId([
                'document_title'=> $request->post('document_title'),
                'document_number'=> $request->post('document_number'),
                'document_category_id'=> $request->post('document_category_id'),
                'document_description'=> $request->post('document_description'),
                'document_priority'=> $request->post('document_priority'),
                'document_status'=> 0,
                'user_id'=> $request->post('user_id'),
                'year_id'=> $request->post('year_id'),
                'timestamp'=> Carbon::now(),
            ]);

            foreach($request->post('send_to') as $user){
                $document_stage = 0;
                if($user['type'] == 'user'){
                    $document_stage = DB::table('document_stage')
                    ->insertGetId([
                        'stage'=> 1,
                        'document_id'=> $document_id,
                        'sender_user_id'=> $request->post('user_id'),
                        'sender_type'=> 'user',
                        'to'=> $user['id'],
                        'status'=> 0,
                        'created_timestamp'=> Carbon::now(),
                        'read_timestamp'=> null,
                    ]);

                    $user_data = DB::table('users')->where('id',$user['id'])->first();
                    $category = DB::table('document_category')->where('id',$request->post('document_category_id'))->first();
                    $url = env('APP_URL')."?redirect=".urlencode('/user/view/').$document_stage;
                    $desp = '';
                    if(!empty($request->post('document_description'))){
                        $desp = $request->post('document_description');
                    }
                    $priority = '';
                    if($request->post('document_priority') == 0){
                        $priority = 'ทั่วไป';
                    }else if($request->post('document_priority') == 1) {
                        $priority = 'ด่วน';
                    }else if($request->post('document_priority') == 2) {
                        $priority = 'ด่วนมาก';
                    }

                    $html =
                    "
                        <h2>แจ้งเตือน มีเอกสารรอรับการตรวจสอบส่งถึงคุณ</h2>
                        <br>
                        <h4><b>ความสำคัญ: </b>$priority</h4>
                        <h4><b>หมวดหมู่: </b>$category->group_name</h4>
                        <h4><b>รายละเอียด: </b>$desp</h4>
                        <br>
                        <b>คลิกที่ Link ด้านล่างเพื่อตรวจสอบ:</b><br>
                        <a href='$url' target='_blank'>
                            $url
                        </a>
                    ";

                    Mail::send([], [], function($message) use ($user_data,$html) {
                        $message->to($user_data->email, 'แจ้งเตือนเอกสารเข้า')
                        ->subject('[Notify] ระบบสารบรรณอิเล็กทรอนิกส์ (E-DOC)');
                        $message->from(env('MAIL_USERNAME'),'[Notify] ระบบสารบรรณอิเล็กทรอนิกส์ (E-DOC)');
                        $message->setBody($html, 'text/html');
                     });

                }else if($user['type'] == 'group'){
                    $user_ingroup = DB::table('user_ingroup')->where('group_id',$user['id'])->get();
                    foreach($user_ingroup as $user_id){
                        $chk2 = DB::table('document_stage')
                                ->where('document_id',$document_id)
                                ->where('sender_type','user')
                                ->where('to',$user_id->user_id)
                                ->where('stage',1)
                                ->first();
                        if(empty($chk2)){
                            $document_stage = DB::table('document_stage')
                            ->insertGetId([
                                'stage'=> 1,
                                'document_id'=> $document_id,
                                'sender_user_id'=> $request->post('user_id'),
                                'sender_type'=> 'user',
                                'to'=> $user_id->user_id,
                                'status'=> 0,
                                'created_timestamp'=> Carbon::now(),
                                'read_timestamp'=> null,
                            ]);

                            $user_data = DB::table('users')->where('id',$user_id->user_id)->first();
                            $category = DB::table('document_category')->where('id',$request->post('document_category_id'))->first();
                            $url = env('APP_URL')."?redirect=".urlencode('/user/view/').$document_stage;
                            $desp = '';
                            if(!empty($request->post('document_description'))){
                                $desp = $request->post('document_description');
                            }
                            $priority = '';
                            if($request->post('document_priority') == 0){
                                $priority = 'ทั่วไป';
                            }else if($request->post('document_priority') == 1) {
                                $priority = 'ด่วน';
                            }else if($request->post('document_priority') == 2) {
                                $priority = 'ด่วนมาก';
                            }

                            $html =
                            "
                                <h2>แจ้งเตือน มีเอกสารรอรับการตรวจสอบส่งถึงคุณ</h2>
                                <br>
                                <h4><b>ความสำคัญ: </b>$priority</h4>
                                <h4><b>หมวดหมู่: </b>$category->group_name</h4>
                                <h4><b>รายละเอียด: </b>$desp</h4>
                                <br>
                                <b>คลิกที่ Link ด้านล่างเพื่อตรวจสอบ:</b><br>
                                <a href='$url' target='_blank'>
                                    $url
                                </a>
                            ";

                            Mail::send([], [], function($message) use ($user_data,$html) {
                                $message->to($user_data->email, 'แจ้งเตือนเอกสารเข้า')
                                ->subject('[Notify] ระบบสารบรรณอิเล็กทรอนิกส์ (E-DOC)');
                                $message->from(env('MAIL_USERNAME'),'[Notify] ระบบสารบรรณอิเล็กทรอนิกส์ (E-DOC)');
                                $message->setBody($html, 'text/html');
                             });
                        }
                    }
                }
            }

            foreach($request->post('files') as $file_data){
                $file_upload = DB::table('document_file')->insert([
                    "file" => $file_data['file'],
                    "document_id" => $document_id,
                    "document_stage_counter" => 1,
                ]);
            };

            return response()->json(['status' => true]);
    }

    public function SenderUpdateSendDocument(Request $request){
        if($this->ChkUser(1) == false && $this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);

            DB::table('documents')
            ->where('id',$request->post('document_id'))
            ->update([
                'document_title'=> $request->post('document_title'),
                'document_number'=> $request->post('document_number'),
                'document_category_id'=> $request->post('document_category_id'),
                'document_description'=> $request->post('document_description'),
                'document_priority'=> $request->post('document_priority'),
                'year_id'=> $request->post('year_id'),
            ]);

            DB::table('document_file')
            ->where('document_id',$request->post('document_id'))
            ->delete();

            DB::table('document_stage')
            ->where('document_id',$request->post('document_id'))
            ->delete();

            foreach($request->post('send_to') as $user){
                $document_stage = 0;
                if($user['type'] == 'user'){
                    $document_stage = DB::table('document_stage')
                    ->insertGetId([
                        'stage'=> 1,
                        'document_id'=> $request->post('document_id'),
                        'sender_user_id'=> $request->post('user_id'),
                        'sender_type'=> 'user',
                        'to'=> $user['id'],
                        'status'=> 0,
                        'created_timestamp'=> Carbon::now(),
                        'read_timestamp'=> null,
                    ]);
                }else if($user['type'] == 'group'){
                    $user_ingroup = DB::table('user_ingroup')->where('group_id',$user['id'])->get();
                    foreach($user_ingroup as $user_id){
                        $chk2 = DB::table('document_stage')
                                ->where('document_id',$request->post('document_id'))
                                ->where('sender_type','user')
                                ->where('to',$user_id->user_id)
                                ->where('stage',1)
                                ->first();
                        if(empty($chk2)){
                            $document_stage = DB::table('document_stage')
                            ->insertGetId([
                                'stage'=> 1,
                                'document_id'=> $request->post('document_id'),
                                'sender_user_id'=> $request->post('user_id'),
                                'sender_type'=> 'user',
                                'to'=> $user_id->user_id,
                                'status'=> 0,
                                'created_timestamp'=> Carbon::now(),
                                'read_timestamp'=> null,
                            ]);

                        }
                    }
                }
            }

            foreach($request->post('files') as $file_data){
                $file_upload = DB::table('document_file')->insert([
                    "file" => $file_data['file'],
                    "document_id" => $request->post('document_id'),
                    "document_stage_counter" => 1,
                ]);
            };

            return response()->json(['status' => true]);
    }

    public function AdminUpdateSendDocument(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);

            DB::table('documents')
            ->where('id',$request->post('document_id'))
            ->update([
                'document_title'=> $request->post('document_title'),
                'document_number'=> $request->post('document_number'),
                'document_category_id'=> $request->post('document_category_id'),
                'document_description'=> $request->post('document_description'),
                'document_priority'=> $request->post('document_priority'),
                'year_id'=> $request->post('year_id'),
            ]);

            return response()->json(['status' => true]);
    }

    public function Sender_Get_MySender(Request $request){
        if($this->ChkUser(1) == false && $this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        $lists = DB::table('documents')
        ->select('*','documents.id as doc_id')
        ->leftJoin('document_category','document_category.id','documents.document_category_id')
        ->where('user_id',$request->post('user_id'))
        ->where('year_id',$request->post('year_id'))
        ->whereIn('document_status',[0,1])
        ->get();
        return response()->json(['status' => true,'lists'=>$lists]);
    }

    public function User_Get_SenderData(Request $request){
        $document_info = DB::table('document_stage')
        ->select('*','document_stage.id as doc_stage_id','documents.id as doc_id')
        ->leftJoin('documents','documents.id','document_stage.document_id')
        ->where('document_stage.id',$request->route('stage_id'))
        ->first();
        $document_file_array = DB::table('document_file')->where('document_id',$document_info->document_id)->where('document_stage_counter',$document_info->stage)->get();

        $chk = DB::table('document_stage')->where('document_stage.id',$request->route('stage_id'))->first();
        if($chk->status == 0){
            $stage_next = DB::table('document_stage')->where('document_id',$document_info->document_id)->orderByDesc('stage')->limit(1)->first();
            if($document_info->stage == $stage_next->stage){
                $stage_next = $document_info->stage + 1;
                $stage_next_data = DB::table('document_stage')
                ->where('document_id',$document_info->document_id)
                ->where('sender_type','user')
                ->where('stage',$stage_next)
                ->first();
                $document_reupload_file_array = Array();
                if(!empty($stage_next_data)){
                    $document_reupload_file_array = DB::table('document_file')->where('document_id',$stage_next_data->document_id)->where('document_stage_counter',$stage_next)->get();
                }else{
                    $document_reupload_file_array = Array();
                }
            }else{
                $stage_next_data = DB::table('document_stage')
                ->where('document_id',$document_info->document_id)
                ->where('sender_type','user')
                ->where('stage',$stage_next->stage)
                ->first();
                $document_reupload_file_array = Array();
                if(!empty($stage_next_data)){
                    $document_reupload_file_array = DB::table('document_file')->where('document_id',$stage_next_data->document_id)->where('document_stage_counter',$stage_next->stage)->get();
                }else{
                    $document_reupload_file_array = Array();
                }
            }
        }else{
            $stage_next = $document_info->stage + 1;
            $stage_next_data = DB::table('document_stage')
            ->where('document_id',$document_info->document_id)
            ->where('sender_type','user')
            ->where('stage',$stage_next)
            ->first();
            $document_reupload_file_array = Array();
            if(!empty($stage_next_data)){
                $document_reupload_file_array = DB::table('document_file')->where('document_id',$stage_next_data->document_id)->where('document_stage_counter',$stage_next)->get();
            }else{
                $document_reupload_file_array = Array();
            }
        }

        return response()->json(['status' => true,'document_info'=>$document_info,'document_file','document_files'=>$document_file_array,'document_reupload_file_array'=>$document_reupload_file_array]);
    }

    public function User_MarkAsRead_Inbox(Request $request){
        $chk = DB::table('document_stage')->where('id',$request->post('document_stage_id'))->first();
        if($chk->status == 0 && $chk->read_timestamp == null){
            DB::table('document_stage')
            ->where('id',$request->post('document_stage_id'))
            ->update([
                'read_timestamp' => Carbon::now()
            ]);
        }
    }

    public function User_Submit_SenderData(Request $request){
        DB::table('document_stage')
        ->where('id',$request->post('document_stage_id'))
        ->update([
            'status' => true,
            'read_timestamp' => Carbon::now()
        ]);

        $top_stage = DB::table('document_stage')->where('document_id',$request->post('document_id'))->orderByDesc('stage')->limit(1)->first();
        $stage = (int) $request->post('stage');
        if($stage == $top_stage->stage){
            $stage += 1;
        }else{
            $stage = $top_stage->stage;
        }

        $sign_check = $request->post('sign_check');
        if($sign_check == true){
            DB::table('documents')
            ->where('id',$request->post('document_id'))
            ->update([
                'sign_timestamp' => Carbon::now()
            ]);
            $document_old_data = DB::table('documents')->where('id',$request->post('document_id'))->first();
            $document_stage = DB::table('document_stage')
           ->insertGetId([
               'stage'=> $stage,
               'document_id'=> $request->post('document_id'),
               'sender_user_id'=> $request->post('user_id'),
               'sender_type'=> 'user',
               'to'=> $document_old_data->user_id,
               'status'=> 0,
               'created_timestamp'=> Carbon::now(),
               'read_timestamp'=> null,
           ]);

            $doc_data = DB::table('documents')->where('id',$request->post('document_id'))->first();
            $user_data = DB::table('users')->where('id',$document_old_data->user_id)->first();
            $category = DB::table('document_category')->where('id',$doc_data->document_category_id)->first();
            $url = env('APP_URL')."?redirect=".urlencode('/user/view/').$document_stage;
            $desp = '';
            if(!empty($request->post('document_description'))){
                $desp = $request->post('document_description');
            }
            $priority = '';
            if($doc_data->document_priority == 0){
                $priority = 'ทั่วไป';
            }else if($doc_data->document_priority == 1) {
                $priority = 'ด่วน';
            }else if($doc_data->document_priority == 2) {
                $priority = 'ด่วนมาก';
            }

            $html =
            "
                <h2>แจ้งเตือน มีเอกสารถึงคุณและได้ลงวันที่แล้ว</h2>
                <br>
                <h4><b>ความสำคัญ: </b>$priority</h4>
                <h4><b>หมวดหมู่: </b>$category->group_name</h4>
                <h4><b>รายละเอียด: </b>$desp</h4>
                <br>
                <b>คลิกที่ Link ด้านล่างเพื่อตรวจสอบ:</b><br>
                <a href='$url' target='_blank'>
                    $url
                </a>
            ";

            Mail::send([], [], function($message) use ($user_data,$html) {
                $message->to($user_data->email, 'แจ้งเตือนเอกสารเข้า')
                ->subject('[Notify] ระบบสารบรรณอิเล็กทรอนิกส์ (E-DOC)');
                $message->from(env('MAIL_USERNAME'),'[Notify] ระบบสารบรรณอิเล็กทรอนิกส์ (E-DOC)');
                $message->setBody($html, 'text/html');
            });

            if($request->post('send_to') != NULL){
                foreach($request->post('send_to') as $user){
                    $document_stage = 0;
                    if($user['type'] == 'user'){
                        $document_stage = DB::table('document_stage')
                        ->insertGetId([
                            'stage'=> $stage,
                            'document_id'=> $request->post('document_id'),
                            'sender_user_id'=> $request->post('user_id'),
                            'sender_type'=> 'user',
                            'to'=> $user['id'],
                            'status'=> 0,
                            'created_timestamp'=> Carbon::now(),
                            'read_timestamp'=> null,
                        ]);

                        $doc_data = DB::table('documents')->where('id',$request->post('document_id'))->first();
                        $user_data = DB::table('users')->where('id',$user['id'])->first();
                        $category = DB::table('document_category')->where('id',$doc_data->document_category_id)->first();
                        $url = env('APP_URL')."?redirect=".urlencode('/user/view/').$document_stage;
                        $desp = '';
                        if(!empty($request->post('document_description'))){
                            $desp = $request->post('document_description');
                        }
                        $priority = '';
                        if($doc_data->document_priority == 0){
                            $priority = 'ทั่วไป';
                        }else if($doc_data->document_priority == 1) {
                            $priority = 'ด่วน';
                        }else if($doc_data->document_priority == 2) {
                            $priority = 'ด่วนมาก';
                        }

                        $html =
                        "
                            <h2>แจ้งเตือน มีเอกสารถึงคุณและได้ลงวันที่แล้ว</h2>
                            <br>
                            <h4><b>ความสำคัญ: </b>$priority</h4>
                            <h4><b>หมวดหมู่: </b>$category->group_name</h4>
                            <h4><b>รายละเอียด: </b>$desp</h4>
                            <br>
                            <b>คลิกที่ Link ด้านล่างเพื่อตรวจสอบ:</b><br>
                            <a href='$url' target='_blank'>
                                $url
                            </a>
                        ";

                        Mail::send([], [], function($message) use ($user_data,$html) {
                            $message->to($user_data->email, 'แจ้งเตือนเอกสารเข้า')
                            ->subject('[Notify] ระบบสารบรรณอิเล็กทรอนิกส์ (E-DOC)');
                            $message->from(env('MAIL_USERNAME'),'[Notify] ระบบสารบรรณอิเล็กทรอนิกส์ (E-DOC)');
                            $message->setBody($html, 'text/html');
                        });
                    }else if($user['type'] == 'group'){
                        $user_ingroup = DB::table('user_ingroup')->where('group_id',$user['id'])->get();
                        foreach($user_ingroup as $user_id){
                            $chk2 = DB::table('document_stage')
                            ->where('document_id',$request->post('document_id'))
                            ->where('sender_type','user')
                            ->where('to',$user_id->user_id)
                            ->where('stage',$stage)
                            ->first();
                            if(empty($chk2)){
                                $document_stage = DB::table('document_stage')
                                ->insertGetId([
                                    'stage'=> $stage,
                                    'document_id'=> $request->post('document_id'),
                                    'sender_user_id'=> $request->post('user_id'),
                                    'sender_type'=> 'user',
                                    'to'=> $user_id->user_id,
                                    'status'=> 0,
                                    'created_timestamp'=> Carbon::now(),
                                    'read_timestamp'=> null,
                                ]);

                                $doc_data = DB::table('documents')->where('id',$request->post('document_id'))->first();
                                $user_data = DB::table('users')->where('id',$user_id->user_id)->first();
                                $category = DB::table('document_category')->where('id',$doc_data->document_category_id)->first();
                                $url = env('APP_URL')."?redirect=".urlencode('/user/view/').$document_stage;
                                $desp = '';
                                if(!empty($request->post('document_description'))){
                                    $desp = $request->post('document_description');
                                }
                                $priority = '';
                                if($doc_data->document_priority == 0){
                                    $priority = 'ทั่วไป';
                                }else if($doc_data->document_priority == 1) {
                                    $priority = 'ด่วน';
                                }else if($doc_data->document_priority == 2) {
                                    $priority = 'ด่วนมาก';
                                }

                                $html =
                                "
                                    <h2>แจ้งเตือน มีเอกสารถึงคุณและได้ลงวันที่แล้ว</h2>
                                    <br>
                                    <h4><b>ความสำคัญ: </b>$priority</h4>
                                    <h4><b>หมวดหมู่: </b>$category->group_name</h4>
                                    <h4><b>รายละเอียด: </b>$desp</h4>
                                    <br>
                                    <b>คลิกที่ Link ด้านล่างเพื่อตรวจสอบ:</b><br>
                                    <a href='$url' target='_blank'>
                                        $url
                                    </a>
                                ";

                                Mail::send([], [], function($message) use ($user_data,$html) {
                                    $message->to($user_data->email, 'แจ้งเตือนเอกสารเข้า')
                                    ->subject('[Notify] ระบบสารบรรณอิเล็กทรอนิกส์ (E-DOC)');
                                    $message->from(env('MAIL_USERNAME'),'[Notify] ระบบสารบรรณอิเล็กทรอนิกส์ (E-DOC)');
                                    $message->setBody($html, 'text/html');
                                });
                            }
                        }
                    }
                }
            }
        } else {
            foreach($request->post('send_to') as $user){
                $document_stage = 0;
                if($user['type'] == 'user'){
                    $chkuser = DB::table('document_stage')->where('document_id',$request->post('document_id'))->where('to',$user['id'])->where('stage',$stage)->where('sender_type','user')->first();
                    $chkuser2 = DB::table('document_stage')->where('document_id',$request->post('document_id'))->where('to',$user['id'])->where('status',0)->where('sender_type','user')->first();
                    if(empty($chkuser2)){
                        if(empty($chkuser)){
                            $document_stage = DB::table('document_stage')
                            ->insertGetId([
                                'stage'=> $stage,
                                'document_id'=> $request->post('document_id'),
                                'sender_user_id'=> $request->post('user_id'),
                                'sender_type'=> 'user',
                                'to'=> $user['id'],
                                'status'=> 0,
                                'created_timestamp'=> Carbon::now(),
                                'read_timestamp'=> null,
                            ]);

                            $doc_data = DB::table('documents')->where('id',$request->post('document_id'))->first();
                            $user_data = DB::table('users')->where('id',$user['id'])->first();
                            $category = DB::table('document_category')->where('id',$doc_data->document_category_id)->first();
                            $url = env('APP_URL')."?redirect=".urlencode('/user/view/').$document_stage;
                            $desp = '';
                            if(!empty($request->post('document_description'))){
                                $desp = $request->post('document_description');
                            }
                            $priority = '';
                            if($doc_data->document_priority == 0){
                                $priority = 'ทั่วไป';
                            }else if($doc_data->document_priority == 1) {
                                $priority = 'ด่วน';
                            }else if($doc_data->document_priority == 2) {
                                $priority = 'ด่วนมาก';
                            }

                            $html =
                            "
                                <h2>แจ้งเตือน มีเอกสารรอรับการตรวจสอบส่งถึงคุณ</h2>
                                <br>
                                <h4><b>ความสำคัญ: </b>$priority</h4>
                                <h4><b>หมวดหมู่: </b>$category->group_name</h4>
                                <h4><b>รายละเอียด: </b>$desp</h4>
                                <br>
                                <b>คลิกที่ Link ด้านล่างเพื่อตรวจสอบ:</b><br>
                                <a href='$url' target='_blank'>
                                    $url
                                </a>
                            ";

                            Mail::send([], [], function($message) use ($user_data,$html) {
                                $message->to($user_data->email, 'แจ้งเตือนเอกสารเข้า')
                                ->subject('[Notify] ระบบสารบรรณอิเล็กทรอนิกส์ (E-DOC)');
                                $message->from(env('MAIL_USERNAME'),'[Notify] ระบบสารบรรณอิเล็กทรอนิกส์ (E-DOC)');
                                $message->setBody($html, 'text/html');
                            });
                        }
                    }
                }else if($user['type'] == 'group'){
                    $user_ingroup = DB::table('user_ingroup')->where('group_id',$user['id'])->get();
                    foreach($user_ingroup as $user_id){
                        $chkuser2 = DB::table('document_stage')->where('document_id',$request->post('document_id'))->where('to',$user_id->user_id)->where('status',0)->where('sender_type','user')->first();
                        if(empty($chkuser2)){
                            $chk2 = DB::table('document_stage')
                            ->where('document_id',$request->post('document_id'))
                            ->where('sender_type','user')
                            ->where('to',$user_id->user_id)
                            ->where('stage',$stage)
                            ->first();
                            if(empty($chk2)){
                                $document_stage = DB::table('document_stage')
                                ->insertGetId([
                                    'stage'=> $stage,
                                    'document_id'=> $request->post('document_id'),
                                    'sender_user_id'=> $request->post('user_id'),
                                    'sender_type'=> 'user',
                                    'to'=> $user_id->user_id,
                                    'status'=> 0,
                                    'created_timestamp'=> Carbon::now(),
                                    'read_timestamp'=> null,
                                ]);

                                $doc_data = DB::table('documents')->where('id',$request->post('document_id'))->first();
                                $user_data = DB::table('users')->where('id',$user_id->user_id)->first();
                                $category = DB::table('document_category')->where('id',$doc_data->document_category_id)->first();
                                $url = env('APP_URL')."?redirect=".urlencode('/user/view/').$document_stage;
                                $desp = '';
                                if(!empty($request->post('document_description'))){
                                    $desp = $request->post('document_description');
                                }
                                $priority = '';
                                if($doc_data->document_priority == 0){
                                    $priority = 'ทั่วไป';
                                }else if($doc_data->document_priority == 1) {
                                    $priority = 'ด่วน';
                                }else if($doc_data->document_priority == 2) {
                                    $priority = 'ด่วนมาก';
                                }

                                $html =
                                "
                                    <h2>แจ้งเตือน มีเอกสารรอรับการตรวจสอบส่งถึงคุณ</h2>
                                    <br>
                                    <h4><b>ความสำคัญ: </b>$priority</h4>
                                    <h4><b>หมวดหมู่: </b>$category->group_name</h4>
                                    <h4><b>รายละเอียด: </b>$desp</h4>
                                    <br>
                                    <b>คลิกที่ Link ด้านล่างเพื่อตรวจสอบ:</b><br>
                                    <a href='$url' target='_blank'>
                                        $url
                                    </a>
                                ";

                                Mail::send([], [], function($message) use ($user_data,$html) {
                                    $message->to($user_data->email, 'แจ้งเตือนเอกสารเข้า')
                                    ->subject('[Notify] ระบบสารบรรณอิเล็กทรอนิกส์ (E-DOC)');
                                    $message->from(env('MAIL_USERNAME'),'[Notify] ระบบสารบรรณอิเล็กทรอนิกส์ (E-DOC)');
                                    $message->setBody($html, 'text/html');
                                });
                            }
                        }
                    }
                }
            }
        }

        DB::table('document_file')
        ->where('document_id',$request->post('document_id'))
        ->where('document_stage_counter',$stage)
        ->delete();

        foreach($request->post('files') as $file_data){
            $file_upload = DB::table('document_file')->insert([
                "file" => $file_data['file'],
                "document_id" => $request->post('document_id'),
                "document_stage_counter" => $stage ,
            ]);
        };

        return response()->json(['status' => true]);
    }

    public function Sender_Get_SenderData(Request $request){
        if($this->ChkUser(1) == false && $this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        $document_info = DB::table('documents')
        ->select('*','documents.id as doc_id')
        ->leftJoin('document_category','document_category.id','documents.document_category_id')
        ->where('documents.id',$request->route('document_id'))
        ->first();

        $document_tracking = Array();
        $load_document_tracking = DB::table('document_stage')
        ->where('document_id',$request->route('document_id'))
        ->get();
        foreach($load_document_tracking as $doc_track){
            $sendby_user = DB::table('users')->where('id',$doc_track->sender_user_id)->first();
            $to_data = null;
            if($doc_track->sender_type == 'user'){
                $to_data = DB::table("users")->where('id',$doc_track->to)->first();
            }else if($doc_track->sender_type == 'group'){
                $to_data = DB::table("user_group")->where('id',$doc_track->to)->first();
            }
            $files = DB::table('document_file')->where('document_id',$doc_track->document_id)->where('document_stage_counter',$doc_track->stage)->get();
            array_push($document_tracking,[
                'id'=> $doc_track->id,
                'stage'=> $doc_track->stage,
                'document_id'=> $doc_track->document_id,
                'sender_user_id'=> $doc_track->sender_user_id,
                'sender_user_data'=> $sendby_user,
                'sender_type'=> $doc_track->sender_type,
                'to'=> $doc_track->to,
                'to_data'=> $to_data,
                'status'=> $doc_track->status,
                'read_timestamp'=> $doc_track->read_timestamp,
                'created_timestamp'=> $doc_track->created_timestamp,
                'files'=> $files,
            ]);
        }

        $document_stage = DB::table('document_stage')
        ->where('document_id',$request->route('document_id'))
        ->distinct()
        ->count('stage');

        return response()->json(['status' => true,'document_info'=>$document_info,'tracking'=>$document_tracking,'stage'=>$document_stage]);
    }

    public function Admin_Get_SenderData(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        $document_info = DB::table('documents')
        ->select('*','documents.id as doc_id')
        ->leftJoin('document_category','document_category.id','documents.document_category_id')
        ->where('documents.id',$request->route('document_id'))
        ->first();

        $document_tracking = Array();
        $load_document_tracking = DB::table('document_stage')
        ->where('document_id',$request->route('document_id'))
        ->get();
        foreach($load_document_tracking as $doc_track){
            $sendby_user = DB::table('users')->where('id',$doc_track->sender_user_id)->first();
            $to_data = null;
            if($doc_track->sender_type == 'user'){
                $to_data = DB::table("users")->where('id',$doc_track->to)->first();
            }else if($doc_track->sender_type == 'group'){
                $to_data = DB::table("user_group")->where('id',$doc_track->to)->first();
            }
            $files = DB::table('document_file')->where('document_stage_id',$doc_track->id)->get();
            array_push($document_tracking,[
                'id'=> $doc_track->id,
                'stage'=> $doc_track->stage,
                'document_id'=> $doc_track->document_id,
                'sender_user_id'=> $doc_track->sender_user_id,
                'sender_user_data'=> $sendby_user,
                'sender_type'=> $doc_track->sender_type,
                'to'=> $doc_track->to,
                'to_data'=> $to_data,
                'status'=> $doc_track->status,
                'read_timestamp'=> $doc_track->read_timestamp,
                'created_timestamp'=> $doc_track->created_timestamp,
                'files'=> $files,
            ]);
        }

        $document_stage = DB::table('document_stage')
        ->where('document_id',$request->route('document_id'))
        ->distinct()
        ->count('stage');

        return response()->json(['status' => true,'document_info'=>$document_info,'tracking'=>$document_tracking,'stage'=>$document_stage]);
    }

    public function SenderCancelDocument(Request $request){
        if($this->ChkUser(2) == false && $this->ChkUser(1) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        DB::table('documents')
        ->where('id',$request->post('document_id'))
        ->update([
            'document_status' => 1
        ]);
        return response()->json(['status' => true]);
    }

    public function SenderDeleteDocument(Request $request){
        if($this->ChkUser(2) == false && $this->ChkUser(1) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        DB::table('documents')
        ->where('id',$request->post('document_id'))
        ->update([
            'document_status' => 2
        ]);
        return response()->json(['status' => true]);
    }

    public function SenderAssignDocument(Request $request){
        if($this->ChkUser(2) == false && $this->ChkUser(1) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        DB::table('documents')
        ->where('id',$request->post('document_id'))
        ->update([
            'document_status' => 0
        ]);
        return response()->json(['status' => true]);
    }

    public function AdminRemoveDocument(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        DB::table('documents')
        ->where('id',$request->post('document_id'))
        ->delete();
        return response()->json(['status' => true]);
    }

    public function Admin_Get_AllSender(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        $lists = DB::table('documents')
        ->select('*','documents.id as doc_id')
        ->leftJoin('document_category','document_category.id','documents.document_category_id')
        ->leftJoin('users','users.id','documents.user_id')
        ->where('year_id',$request->post('year_id'))
        ->get();
        return response()->json(['status' => true,'lists'=>$lists]);
    }

    public function Admin_CreateAcd_Year(Request $request){
        if($this->ChkUser(2) == false)return response()->json(['status' => false,'message' => 'Not Permission']);
        $chk = DB::table('year_list')->where('year',$request->post('year'))->first();
        if(empty($chk)){
            DB::table('year_list')
            ->insert([
                'year' => $request->post('year')
            ]);
            return response()->json(['status' => true]);
        }else{
            return response()->json(['status' => false]);
        }
    }

    public function Admin_RemoveAcd_Year(Request $request){
        $del = DB::table('year_list')
        ->where('id',$request->post('year_id'))
        ->delete();

        if($del){
            return response()->json(['status' => true]);
        }else{
            return response()->json(['status' => false]);
        }
    }

    public function Admin_EditMainAcd_Year(Request $request){
        DB::table('config')
        ->where('config','year_config')
        ->update([
            'value'=> $request->post('year_id')
        ]);
        return response()->json(['status' => true]);
    }

    //Chk User Function
    public function ChkUser($req_permission){
        $_user = auth()->user();
        if(!$_user){
            return false;
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