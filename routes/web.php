<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\HCListController;
use App\Http\Controllers\CaseController;
use App\Http\Controllers\EvaluationController;
use App\Http\Controllers\EmployeeListController;
use App\Http\Controllers\NHIServiceController;

// Route::get('/', function () { return view('welcome'); });
Route::get('/',  [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'processLogin'])->name('login.process');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');

Route::post('/session/extend', function (Request $request) {
  Session::put('last_activity', now()); // 更新 Session 活動時間
  return response()->json(['message' => 'Session extended']);
})->name('session.extend');

// 受保護的頁面
Route::middleware(['auth.session'])->group(function () {
  Route::get('/dashboard', function () {
      return view('dashboard');
  })->name('dashboard');
  Route::get('/layouts/{subcateID}', [\App\Http\Controllers\FormController::class, 'show_list'])->name('layouts.formoptions');
  Route::get('/hc-openlist', [HCListController::class, 'HC_Openlist'])->name('hc-openlist');
  Route::get('/hc-create', [HCListController::class, 'HC_Create'])->name('hc-create');
  Route::get('/hc-closelist', [HCListController::class, 'HC_Closelist'])->name('hc-closelist');
  Route::put('/update-case/{id}', [CaseController::class, 'case_update']);
  Route::put('/update-area/{id}', [CaseController::class, 'case_update_area']);
  Route::post('/new-case', [CaseController::class, 'case_new'])->name('hc-case_new');
  Route::post('/check-id-number', [CaseController::class, 'checkIdNumber'])->name('check-id-number');
  Route::post('/close-case/{id}', [CaseController::class, 'case_close'])->name('hc-case_close');
  Route::post('/reopen-case/{id}', [CaseController::class, 'reopen_case']);
  Route::post('/delete-close/{id}', [CaseController::class, 'delete_close'])->name('hc-delete_close');
  Route::get('/get-evaluation-dates/{formID}/{caseID}', [EvaluationController::class, 'getEvaluationDates']);
  Route::get('/hcevaluation/{formID}/{caseID}/{date?}', [EvaluationController::class, 'showEvaluationForm'])->name('hcevaluation.edit');  //{date?} → date 是可選的，如果沒有日期，就顯示「無紀錄的表單」
  Route::get('/icd/lookup', [\App\Http\Controllers\ICDController::class, 'lookup'])->name('icd.lookup');
  Route::get('/print/hcevaluation/{formID}/{caseID}/{date?}', [EvaluationController::class, 'print'])->name('hcevaluation.print');
  Route::post('/hcevaluation/save', [EvaluationController::class, 'save'])->name('hcevaluation.save');
  Route::get('/personnel/employeelist', [EmployeeListController::class, 'showlist']);
  Route::get('/personnel/{formID}/{employeeID}', [EmployeeListController::class, 'showForm']);
  Route::get('/nhiservice/registration', [NHIServiceController::class, 'registration_list'])->name('registration_list'); //健保掛號列表
  Route::get('/nhiservice/reginfo/{REGID?}', [NHIServiceController::class, 'reginfo'])->name('reginfo'); //健保掛號

  Route::get('/nhiservice/treatment_maintain', [NHIServiceController::class, 'treatment_maintance'])->name('treatment_maintance'); //健保處置代碼列表
  Route::post('/newtreatment', [NHIServiceController::class, 'new_treatment'])->name('new_treatment');
  Route::get('/get_treatment/{id}', [NHIServiceController::class, 'get_treatment'])->name('get_treatment');
  Route::put('/update_treatment/{id}', [NHIServiceController::class, 'update_treatment'])->name('update_treatment');
  Route::post('/delete_treatment/{id}', [NHIServiceController::class, 'delete_treatment'])->name('delete_treatment');
  Route::get('/nhiservice/treatment_set', [NHIServiceController::class, 'treatment_setting'])->name('treatment_set'); //健保處置套組列表
  Route::get('/nhiservice/treatment_set_page/{set_id?}', [NHIServiceController::class, 'treatment_set_edit'])->name('nhiservice.treatment_set_edit');
  Route::post('/nhiservice/treatment_set_page/save', [NHIServiceController::class, 'save_treatment_set'])->name('treatment_set_save');
  Route::post('/delete_treatment_set/{set_id}', [NHIServiceController::class, 'delete_treatment_set'])->name('delete_treatment_set');
});


