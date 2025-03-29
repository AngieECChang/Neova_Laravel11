<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use App\Services\DatabaseConnectionService;

class EmployeeDataMiddleware
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

    $open_staffs_withIdNo  = $db->table('employees')
    ->whereNotNull('IdNo')
    ->where('IdNo', '!=', '')
    ->where('status', 1)
    ->orderBy('employeeNo')
    ->orderBy('employeeID')
    ->get();

    $quit_staffs_withIdNo  = $db->table('employees')
    ->whereNotNull('IdNo')
    ->where('IdNo', '!=', '')
    ->where('status', 0)
    ->orderBy('employeeNo')
    ->orderBy('employeeID')
    ->get();

    $all_staffs  = $db->table('employees')
    ->whereNotNull('IdNo')
    ->where('IdNo', '!=', '')
    ->orderBy('employeeNo')
    ->orderBy('employeeID')
    ->get();

    $all_staffs_array = $all_staffs->keyBy('employeeID')->toArray();

    // 共享數據到所有視圖
    View::share('open_staffs_withIdNo', $open_staffs_withIdNo);
    View::share('quit_staffs_withIdNo', $quit_staffs_withIdNo);
    View::share('all_staffs_array', $all_staffs_array);

    return $next($request);
  }
}
