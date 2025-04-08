<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Services\DatabaseConnectionService;

class HCManagementController extends Controller
{
  public function showForm(Request $request, $formID)
  {
    $db = DatabaseConnectionService::setConnection('nvhcs-1');

    if($formID=="form01"){
      $result = $db->table('client_info')
      ->where('client_id', session('client_id'))
      ->first();
      return view('hcmanagement.'.$formID, compact('result'));
    }
  }

  public function saveForm(Request $request)
  {
    $db = DatabaseConnectionService::setConnection('nvhcs-1');
    $formID = $request->input('formID');

    $formData = $request->except(['_token', 'formID']);
    //把 $formData 陣列中所有 null 的欄位都換成空字串（''），其他欄位保持原樣
    $formData = array_map(function ($value) {
      return is_null($value) ? '' : $value;
    }, $formData);
    
    if($formID=="form01"){
      $db->table('client_info')->where('client_id', session('client_id'))->update($formData);
    }
    return redirect()->back()->with('success', '資料已成功儲存');
  }
}