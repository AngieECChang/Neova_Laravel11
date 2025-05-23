<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Services\DatabaseConnectionService;

class ClientDataMiddleware
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
    $area = $db->table('area_info')
      ->select('areaID', 'areaName')
      ->orderBy('area_order')
      ->get();

    // 轉換成 key-value 陣列
    // $area = [];
    // foreach ($area_arrayinfo as $a) {
    //   $area[$a->areaID] = $a->areaName;
    // }
    $area_arrayinfo = $area->pluck('areaName', 'areaID')->toArray();

    $treatment_lists = $db->table('nhiservice_treatment')
    ->orderBy('category')
    ->orderBy('short_code')
    ->get();
    //用id當作鍵值，先將每一筆轉成陣列，避免出現Cannot use object of type stdClass as array錯誤
    $treatment_item_arrayinfo = $treatment_lists
      ->map(function ($item) {
          return (array) $item;
      })
      ->keyBy('id')
      ->toArray();

    // 共享數據到所有視圖
    View::share('area_arrayinfo', $area_arrayinfo);
    View::share('treatment_item_arrayinfo', $treatment_item_arrayinfo);
    return $next($request);
  }
}
