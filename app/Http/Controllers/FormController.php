<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\DatabaseConnectionService;

class FormController extends Controller
{
  public function show_list($subcateID)
  {
    $permission_items = DB::table('user_permission as a')
      ->leftJoin('permission_item as b', 'a.itemID', '=', 'b.itemID')
      ->leftJoin('permission_subcate as c', 'b.subcateID', '=', 'c.subcateID')
      ->leftJoin('permission_cate as d', 'c.cateID', '=', 'd.cateID')
      ->where('b.subcateID',$subcateID)
      ->select(
        'a.*',
        'b.*',
        'c.name as subcate_name',
        'd.cate_shortname'
      )
      ->orderBy('b.order')
      ->get();
    // dd($permission_items);
    return view('layouts.formoptions', compact('permission_items'));
  }
}