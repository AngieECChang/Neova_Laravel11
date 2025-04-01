<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Services\DatabaseConnectionService;

class NHIServiceController extends Controller
{
  public function registration_list(Request $request)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    return view('nhiservice.registration');
  }

  public function treatment_maintance(Request $request)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    $treatment_lists = $db->table('nhiservice_treatment')
      ->orderBy('category')
      ->orderBy('short_code');
      // 分類 caseType -> areaName
    $treatment_list = $treatment_lists->get()->groupBy(['category']);

    // dd($treatment_list->toArray());

    return view('nhiservice.treatment_maintain', compact('treatment_list'));
  }

  public function new_treatment(Request $request)
  {
    try {
      $databaseName = session('DB'); // 可根據條件動態變更
      $db = DatabaseConnectionService::setConnection($databaseName);
      // dd($request->all());
      // 驗證輸入資料
      $validator = Validator::make($request->all(),[
        'short_code' => 'required|string|max:50',
        'treatment_code' => 'required|string|max:30',
        'treatment_name_zh' => 'required|string|max:500',
        'start_date' => 'required|date_format:Y-m-d', // 西元年格式
        'end_date' => 'required|date_format:Y-m-d', // 西元年格式
        'points' => 'required|string|max:20',
        'category' => 'required|string|max:20',
        'group' => 'required|array',
        'group.*' => 'in:1,2,3'
      ], [
        '*.required' => ':attribute 為必填欄位',
        'start_date.required' => '請輸入日期',
        'start_date.date_format' => '請輸入正確格式：西元年-月-日（例如 2025-04-01）',
        'end_date.required' => '請輸入日期',
        'end_date.date_format' => '請輸入正確格式：西元年-月-日（例如 2025-04-01）',
      ]);

      if ($validator->fails()) {
        return response()->json([
          'success' => false,
          'errors' => $validator->errors()
        ], 422);
      }

      $allowedFields = ['short_code', 'treatment_code', 'treatment_name_zh', 'treatment_name_en', 'model_no', 'unit', 'points', 'start_date', 'end_date', 'comments', 'category', 'group'];
      $postData = collect($request->except(['_token']));
      // 指定多選欄位名稱（可依你表單實際欄位調整），處理多選欄位
      $multiSelectFields = ['group'];
      foreach ($multiSelectFields as $field) {
        if (isset($postData[$field]) && is_array($postData[$field])) {
          // 將陣列轉成逗號分隔字串
          $postData[$field] = implode(';', $postData[$field]);
        }
      }
      // null 轉成空字串（保持 Collection 狀態）
      $postData = $postData->map(function ($value) {
        return is_null($value) ? '' : $value;
      });

      $filteredData = $postData->only($allowedFields)->toArray();
      $filteredData['created_by'] = session('user_id') ?? 'Auto';
      
      $db->table('nhiservice_treatment')
        ->insert($filteredData);

      return response()->json(['success' => true]);
    }
    catch (\Exception $e) {
      \Log::error('新增失敗：' . $e->getMessage());
      return response()->json([
        'success' => false,
        'errors' => ['系統錯誤：' . $e->getMessage()]
      ], 500);
    }
  }
}