<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Services\DatabaseConnectionService;

class EmployeeListController extends Controller
{
  public function showlist(Request $request)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    $open_staffs  = $db->table('employees')
      ->select('*')
      ->where('status',1)
      ->orderBy('employeeNo')
      ->orderBy('employeeID')
      ->get();

    $quit_staffs  = $db->table('employees')
    ->select('*')
    ->where('status',0)
    ->orderBy('employeeNo')
    ->orderBy('employeeID')
    ->get();

    return view('personnel.employeelist', compact('open_staffs', 'quit_staffs'));
  }

  public function showForm($formID, $employeeID)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);
    $form = "employees";

    $employee_info = $db->table('employees')
    ->where('employeeID', $employeeID)
    ->first(); // 只取出第一筆符合條件的資料

    return view('personnel.form01', compact('employee_info'));
  }
}