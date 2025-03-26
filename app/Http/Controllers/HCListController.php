<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\DatabaseConnectionService;

class HCListController extends Controller
{
  public function HC_Openlist(Request $request)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    $region = $request->input('region');
    $areaNames = $db->table('area_info')
    ->select('areaID', 'areaName')
    ->distinct()
    ->orderBy('area_order')
    ->pluck('areaName', 'areaID');

    $open_cases_query  = $db->table('case_open as a')
      ->leftJoin('cases as b', 'a.caseID', '=', 'b.caseID')
      ->join('bed_info as c', 'a.bedID', '=', 'c.bedID')
      ->join('area_info as d', 'c.areaID', '=', 'd.areaID')
      ->select(
        'a.*',
        'b.*',
        'c.bedID',
        'c.RP_user_id',
        'd.areaID',
        'd.areaName'
      )
      ->orderByRaw('CAST(b.case_type AS UNSIGNED)') // 強制轉數字排序
      ->orderBy('d.area_order')
      ->orderBy('b.caseNoDisplay')
      ->orderBy('a.open_date');

    // 如果有篩選特定區域
    if (!empty($region)) {
      $open_cases_query->where('d.areaName', $region);
    }

    // 分類 caseType -> areaName
    $open_cases = $open_cases_query->get()->groupBy(['case_type', 'areaName']);

    //dd($open_cases ->toArray());
    return view('hc-openlist', compact('open_cases', 'region', 'areaNames'));
  }

  public function HC_Closelist()
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

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