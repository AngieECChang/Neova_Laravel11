<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\DatabaseConnectionService;

class EvaluationController extends Controller
{
  // public function show($formID, $caseID)
  // {
  //   $databaseName = session('DB'); // 可根據條件動態變更
  //   $db = DatabaseConnectionService::setConnection($databaseName);

  //   $form = str_replace("form","hcevaluation",$formID);

  //   $the_case = $db->table('cases')
  //     ->where('caseID', $caseID)
  //     ->first(); // 只取出第一筆符合條件的資料

  //   if(!$the_case){
  //     $the_case = array_fill_keys(
  //       \Schema::getColumnListing('cases'), 
  //       null
  //     );
  //   }
  //   $result = $db->table($form)
  //   ->where('caseID', $caseID)
  //   ->orderByDesc('date')
  //   ->get();

  //   if(!$result){
  //     $result = array_fill_keys(
  //       \Schema::getColumnListing($form), 
  //       null
  //     );
  //   }
      
  //   // 取得住院及結案資訊
  //   $case_open = $db->table('case_open')->where('caseID', $caseID)->orderByDesc('open_date')->first();
  //   $case_closed = $db->table('case_closed')->where('caseID', $caseID)->orderByDesc('close_date')->first();

  //   //dd($open_cases ->toArray());
  //   return view('hcevaluation.'. $formID, compact('the_case', 'result', 'case_open', 'case_closed'));
  // }

  public function getEvaluationDates($formID, $caseID)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);
    $form = str_replace("form","hcevaluation",$formID);

    $dates = $db->table($form)
      ->where('caseID', $caseID)
      ->orderBy('date', 'desc')
      ->pluck('date');
    // **如果沒有紀錄，直接返回 `no_records`**
    if ($dates->isEmpty()) {
      return response()->json(['no_records' => true]);
    }
    return response()->json($dates);
  }

  public function showEvaluationForm($formID, $caseID, $date = null)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);
    $form = str_replace("form","hcevaluation",$formID);
   
    $the_case = $db->table('cases')
    ->where('caseID', $caseID)
    ->first(); // 只取出第一筆符合條件的資料

    if(!$the_case){
      // 確保 $the_case 為 Collection，而不是純陣列
      $the_case =  (object)array_fill_keys(\Schema::getColumnListing('cases'), null);
    }

    if($date!=""){
      $result = $db->table($form)
      ->where('caseID', $caseID)
      ->where('date', $date)
      ->first();
    }else{
      $result = $db->table($form)
      ->where('caseID', $caseID)
      ->orderByDesc('date')
      ->first();
    }

    if(!$result){
      $result =  (object)array_fill_keys(\Schema::getColumnListing($form), null);
    }
    // 取得住院及結案資訊
    $case_open = $db->table('case_open')->where('caseID', $caseID)->orderByDesc('open_date')->first();
    $case_closed = $db->table('case_closed')->where('caseID', $caseID)->orderByDesc('close_date')->first();

    return view('hcevaluation.'.$formID, compact('the_case', 'result', 'case_open', 'case_closed', 'formID', 'caseID', 'date'));
  }

  public function save(Request $request)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);
    // 直接取得輸入的資料，沒有自動驗證
    $data_array = $request->except('_token');  //取得所有數據但排除 _token
    dd($data_array);
    $close_cases = $db->table('case_closed as a')
      ->leftJoin('cases as b', 'a.caseID', '=', 'b.caseID')
      ->select(
        'a.*',
        'b.*',
        'a.type as close_type'
      )
      ->orderBy('a.close_date')
      ->orderBy('b.caseNoDisplay')
      ->orderBy('a.open_date')
      ->get();
    //dd($close_cases->toArray());
    // 取得所有 `case_open` 內的 caseID
    $open_case_ids = $db->table('case_open')
        ->pluck('caseID') // 取得 `caseID` 列表
        ->toArray(); // 轉成 PHP 陣列
      
    return view('hc-closelist', compact('close_cases', 'open_case_ids'));
  }
}