<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Schema;
use App\Services\DatabaseConnectionService;

class NHIServiceController extends Controller
{
  public function registration_list(Request $request)
  {
    $request->validate([
      'startdate' => 'nullable|date|before_or_equal:enddate',
      'enddate'   => 'nullable|date|after_or_equal:startdate',
    ], [
      'startdate.before_or_equal' => '起始日期不能晚於結束日期',
      'enddate.after_or_equal'    => '結束日期不能早於起始日期',
      'startdate.date'            => '起始日期格式錯誤',
      'enddate.date'              => '結束日期格式錯誤',
    ]);

    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    $start_date = $request->input('startdate')??date("Y-m-01");
    $end_date = $request->input('enddate')?? date("Y-m-t");

    $start_datetime = $start_date . ' 00:00:00';
    $end_datetime = $end_date . ' 23:59:59';

    $registration_list = $db->table('nhiservice01 as a')
      ->leftJoin('nhiservice02 as b', 'a.REGID', '=', 'b.REGID')
      ->leftJoin('cases as c', 'a.caseID', '=', 'c.caseID')
      ->select(
          'a.*',
          'b.*',
          'c.name',
        )
      ->whereBetween('a.A17', [$start_datetime, $end_datetime])
      ->groupBy(['a.REGID'])
      ->orderBy('a.A17')
      ->get();

    // dd($registration_list);
    return view('nhiservice.registration', compact('registration_list'));
  }

  public function reginfo(Request $request, $REGID=null)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);
    
    if($REGID!=""){
      $result = $db->table('nhiservice01 as a')
        ->leftJoin('nhiservice02 as b', 'a.REGID', '=', 'b.REGID')
        ->where('a.REGID', $REGID)
        ->first();
    } else {
      $columns1 = Schema::getColumnListing('nhiservice01');
      $columns2 = Schema::getColumnListing('nhiservice02');

      $allColumns = array_unique(array_merge($columns1, $columns2));

      $resultArray = array_fill_keys($allColumns, '');
      $result = (object) $resultArray;
    }

    // dd($registration_list);
    return view('nhiservice.reginfo', compact('result'));
  }

  public function getCaseReginfo(Request $request)
  {
    $caseID = strtoupper($request->input('caseID'));

    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    try {
      $lastRecord = $db->table('nhiservice01')
      ->where('caseID',$caseID)
      ->where('finished','1')
      ->orderByDesc('A17')
      ->first();

      $case_data = $db->table('cases')
      ->where('caseID',$caseID)
      ->first();

      $case_type = $case_data->case_type;
      $nhiArray = config('public.nhi_array');
      $d4 = $nhiArray[$case_type]['d4'] ?? null;

      if ($lastRecord) {
        return response()->json(['last_date' => $lastRecord->A17." (".$lastRecord->A23.")", 'last_D8'=> $lastRecord->D8, 'this_D4'=> $d4]);
      } else {
         return response()->json(['last_date' => null, 'last_D8'=> null, 'this_D4'=>null]);
      }
    } catch (\Exception $e) {
      // 如果真的錯了，把錯誤丟回去看訊息
      return response()->json(['success' => false,'message' => '伺服器錯誤：' . $e->getMessage()], 500);
    }
  }

  public function save_reginfo(Request $request)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    $caseID = $request->input('firmID'); //個案
    $shift = $request->input('shift')??''; //申報補件
    $A17 = $request->input('A17')??date('Y-m-d H:i:s'); //就診日期
    $A12 = $request->input('A12')??''; //	身分證號
    $A13 = $request->input('A13')??''; //出生日期
    $A23 = $request->input('A23')??''; //就診類別
    $D1 = $request->input('D1')??''; //案件分類
    $D4 = $request->input('D4')??''; //特療項目1
    $D8 = $request->input('D8')??''; //就醫科別
    $night_plus = $request->input('night_plus')??''; //夜間加成
    $hospID = $request->input('HospID')??'';
    $case_type = $request->input('type')??'';

    $data = [
      'A00' => '1',
      'A01' => '2',  //異常上傳(無卡掛號)
      'A02' => '1.0',
      't5' => '1',
      'A99' => '1',
      'finished' => '0',
      'status' => '1',
      'caseID' => $caseID,
      'case_type' => $case_type,
      'night_plus' => $night_plus,
      'A12' => $A12,
      'A13' => $A13,
      'A14' => $hospID,
      'A17' => $A17,
      'A19' => '',
      'A23' => $A23,
      'shift' => $shift,
      'D1' => $D1,
      'D4' => $D4,
      'D8' => $D8,
      'created_by' => session('user_id')
    ];
    try {
      $insert = $db->table('nhiservice01')->insert($data);
      return redirect()->back()->with('success', '資料已成功儲存');

    } catch (\Exception $e) {
      return response()->json(['success' => false,'message' => '伺服器錯誤：' . $e->getMessage()], 500);
    }
  }

  public function treatment_maintance(Request $request)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    $treatment_lists = $db->table('nhiservice_treatment')
      ->orderBy('category')
      ->orderBy('short_code');
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
          // 將陣列轉成分號分隔字串
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

  public function get_treatment($id)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    $get_treatment = $db->table('nhiservice_treatment')
      ->where('id',$id)
      ->first();
    $get_treatment->group = explode(';', rtrim($get_treatment->group,';'));
    return response()->json($get_treatment);
  }

  public function update_treatment(Request $request, $id)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

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
        // 將陣列轉成分號分隔字串
        $postData[$field] = implode(';', $postData[$field]);
      }
    }
    // null 轉成空字串（保持 Collection 狀態）
    $postData = $postData->map(function ($value) {
      return is_null($value) ? '' : $value;
    });

    $filteredData = $postData->only($allowedFields)->toArray();
    $filteredData['updated_by'] = session('user_id') ?? 'Auto';
      
    $affected = $db->table('nhiservice_treatment')
    ->where('id', $id)
    ->update($filteredData);

    if ($affected) {
      return response()->json(['success' => true, 'message' => '更新成功']);
    } else {
      return response()->json(['success' => false, 'errors' => '更新失敗']);
    }
  }

  public function delete_treatment(Request $request, $id)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);
      
    $affected = $db->table('nhiservice_treatment')
    ->where('id', $id)
    ->update([
      'category' => '99',
      'updated_by' => session('user_id')
    ]);

    if ($affected) {
      return response()->json(['success' => true, 'message' => '刪除成功']);
    } else {
      return response()->json(['success' => false, 'errors' => '刪除失敗']);
    }
  }

  public function treatment_setting(Request $request)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    $treatment_set_lists = $db->table('nhiservice_treatment_set as a')
      ->leftjoin('nhiservice_treatment as b', 'a.nhiservice_treatment_id', '=', 'b.id')
      ->select(
        'a.*',
        'b.treatment_code',
        'b.treatment_name_zh',
      )
      ->orderBy('set_id')
      ->orderBy('sort');

    $treatment_set_list = $treatment_set_lists->get()->groupBy(['set_id']);
    return view('nhiservice.treatment_set', compact('treatment_set_list'));
  }

  public function treatment_set_edit(Request $request, $set_id = null)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    if($set_id!=""){
      $result = $db->table('nhiservice_treatment_set')
      ->where('set_id', $set_id)
      ->orderBy('sort')
      ->get();

      $treatment_set_lists = $db->table('nhiservice_treatment_set as a')
      ->leftjoin('nhiservice_treatment as b', 'a.nhiservice_treatment_id', '=', 'b.id')
      ->select(
        'a.*',
        'b.treatment_code',
        'b.treatment_name_zh',
        'b.short_code'
      )
      ->where('set_id', $set_id)
      ->orderBy('sort')
      ->get();

    }else{
      $result = [(object)array_fill_keys(\Schema::getColumnListing('nhiservice_treatment_set'), null)];
      $treatment_set_lists = collect(); // 回傳空的 Laravel Collection
    }

    $treatment_items = $db->table('nhiservice_treatment')
    ->where('category','!=', '99')
    ->orderBy('short_code')
    ->get();

    return view('nhiservice.treatment_set_page', compact('result', 'treatment_items','treatment_set_lists'));
  }

  public function save_treatment_set(Request $request)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    $new_set_id = ($db->table('nhiservice_treatment_set')->max('set_id') ?? 0) + 1;
    // dd($request);
    
    $set_id = $request->input('set_id');
    $description = $request->input('description');

    $write_set_id = ($set_id!="" && $set_id!="0"?$set_id:$new_set_id);

    $deletedIds = array_filter(array_map('trim', explode(',', (string) $request->input('oldtreatment_deleted_ids', ''))));
    foreach ($deletedIds as $id) {
      if (!empty($id)) {
        $db->table('nhiservice_treatment_set')->where('id', $id)->delete();
      }
    }
    $infoNos = array_filter(array_map('trim', explode(',', $request->input('oldtreatment_infoNo', ''))));
    $oldtreatment_id = $request->input('oldtreatment_id', []);    
    foreach ($infoNos as $i => $id) {
      if (!isset($oldtreatment_id[$id])) continue;
      $short_code = trim($request->oldtreatment_code[$id]);
      if ($short_code === '') {
        $db->table('nhiservice_treatment_set')->where('id', $id)->delete();
        continue;
      }
      $db->table('nhiservice_treatment_set')->where('id', $id)->update([
        'description' => $description,
        'nhiservice_treatment_id' => $request->oldtreatment_id[$id]?? '',
        'sort' => $request->oldtreatment_sort[$id]?? '',
        'updated_by' => session('user_id'),
        'updated_at' => now()
      ]);
    }

    //處置套組項目，新增
    $short_code = $request->input('treatment_item', []);
    $treatment_code = $request->input('treatment_code', []);
    $treatment_name = $request->input('treatment_name', []);
    $treatment_sort = $request->input('treatment_sort', []);
    $treatment_id = $request->input('treatment_id', []);
    foreach ($short_code as $i => $code) {
      if (trim($code) === '') continue; // 跳過空白
      $db->table('nhiservice_treatment_set')->insert([
        'set_id'         => $write_set_id,
        'description'    => $description ?? '',
        'nhiservice_treatment_id' => $treatment_id[$i] ?? '',
        'sort'       => $treatment_sort[$i] ?? '',
        'created_by'     => session('user_id'),
        'created_at'     => now()
      ]);
    }
    return redirect()->route('treatment_set')->with('success', '處置套組儲存成功！');
  }

  public function delete_treatment_set(Request $request, $set_id)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);
      
    $affected = $db->table('nhiservice_treatment_set')
    ->where('set_id', $set_id)
    ->delete();

    if ($affected) {
      return response()->json(['success' => true, 'message' => '刪除成功']);
    } else {
      return response()->json(['success' => false, 'errors' => '刪除失敗']);
    }
  }
}