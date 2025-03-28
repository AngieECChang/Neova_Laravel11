<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Services\DatabaseConnectionService;

class LoadEmployeesByIdNo
{
  public function handle(Request $request, Closure $next)
  {
    $databaseName = session('DB');
    $db = DatabaseConnectionService::setConnection($databaseName);

    $employees = $db->table('employees')
      ->whereNotNull('IdNo')
      ->where('IdNo', '!=', '')
      ->where('status', 1)
      ->orderBy('employeeNo')
      ->orderBy('employeeID')
      ->get();

    $selected_id = $request->input('employeeID');

    $selected_quit = null;
    if ($selected_id) {
      $selected_quit = $db->table('employees')
        ->where('employeeID', $selected_id)
        ->where('status', 0)
        ->whereNotNull('IdNo')
        ->where('IdNo', '!=', '')
        ->first();
    }

    // 傳遞給 view
    View::share('employeesListByIdNo', $employees);
    View::share('selected_employeesByIdNo_quit', $selected_quit);
    View::share('selected_employeesByIdNo_id', $selected_id);
    return $next($request);
  }
}
