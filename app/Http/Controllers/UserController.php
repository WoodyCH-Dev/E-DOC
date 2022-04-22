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
}