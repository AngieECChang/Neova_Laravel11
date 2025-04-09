<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Services\DatabaseConnectionService;

class CaseDataMiddleware
{
  public function handle(Request $request, Closure $next)
  {
    $databaseName = session('DB'); // 可根據條件動態變更

    if (!$databaseName) {
      return $next($request); // 如果沒有 DB 設定，直接進入下一步
    }
    $db = DatabaseConnectionService::setConnection($databaseName);
    if (!$db) {
      return $next($request); // 避免出錯時影響請求
    } 

    $open_cases= $db->table('case_open as a')
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
      ->orderBy('b.caseNoDisplay')
      ->get();

      $subquery = $db->table('hcevaluation01')
      ->select('caseID', $db->raw('MAX(date) as latest'))
      ->groupBy('caseID');

      $latest_baseform = $db->table('hcevaluation01 as e')
        ->joinSub($subquery, 'latest_eval', function ($join) {
            $join->on('e.caseID', '=', 'latest_eval.caseID')
                ->on('e.date', '=', 'latest_eval.latest');
        })
        ->get()
        ->keyBy('caseID');  //用 caseID 當作 key
      $latest_baseform = $latest_baseform->map(fn($item) => (array) $item)->toArray();

    // 共享數據到所有視圖
    View::share('open_cases', $open_cases);
    View::share('cases_latest_baseform', $latest_baseform);
    return $next($request);
  }
}
