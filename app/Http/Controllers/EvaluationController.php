<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Services\DatabaseConnectionService;

class EvaluationController extends Controller
{
  public function getEvaluationDates($formID, $caseID)
  {
    try {
      $databaseName = session('DB'); // 可根據條件動態變更
      if (!$databaseName) {
        return response()->json(['error' => 'No database session found'], 500);
      }
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
    }catch (\Exception $e) {
      \Log::error("🚨 取得個案評估日期失敗", [
          'formID' => $formID,
          'caseID' => $caseID,
          'error' => $e->getMessage()
      ]);

      return response()->json(['error' => '伺服器發生錯誤'], 500);
    }
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

      if($formID=="form01"){
        $medical_result = $db->table('hcevaluation01_medicals')
        ->where('caseID', $caseID)
        ->where('date', $date)
        ->orderBy('CareDate')
        ->get();

        
        // $relative_result = $db->table('hcevaluation01_relatives')
        // ->where('caseID', $caseID)
        // ->where('date', $date)
        // ->get();
      }
    }else{
      $result = $db->table($form)
      ->where('caseID', $caseID)
      ->orderByDesc('date')
      ->first();
    }

    if(!$result){
      $result = (object)array_fill_keys(\Schema::getColumnListing($form), null);
    }
    if($formID=="form01"){
      if(empty($medical_result)){
        $medical_result = (object)array_fill_keys(\Schema::getColumnListing('hcevaluation01_medicals'), null);
      }
      // if(empty($relative_result)){
      //   $relative_result = (object)array_fill_keys(\Schema::getColumnListing('hcevaluation01_relatives'), null);
      // }
    }

    // 取得住院及結案資訊
    $case_open = $db->table('case_open')->where('caseID', $caseID)->orderByDesc('open_date')->first();
    $case_closed = $db->table('case_closed')->where('caseID', $caseID)->orderByDesc('close_date')->first();

    if($formID=="form01"){
      return view('hcevaluation.'.$formID, compact('the_case', 'result', 'case_open', 'case_closed', 'formID', 'caseID', 'date', 'medical_result'));
    }else{
      return view('hcevaluation.'.$formID, compact('the_case', 'result', 'case_open', 'case_closed', 'formID', 'caseID', 'date'));
    }
  }

  public function print($formID, $caseID, $date = null)
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
      if($formID=="form01"){
        $medical_result = $db->table('hcevaluation01_medicals')
        ->where('caseID', $caseID)
        ->where('date', $date)
        ->first();
        
        $relative_result = $db->table('hcevaluation01_relatives')
        ->where('caseID', $caseID)
        ->where('date', $date)
        ->first();
      }
    }else{
      $result = $db->table($form)
      ->where('caseID', $caseID)
      ->orderByDesc('date')
      ->first();
    }
    if($formID=="form01"){
      if(!$medical_result){
        $medical_result = (object)array_fill_keys(\Schema::getColumnListing('hcevaluation01_medicals'), null);
      }
      if(!$relative_result){
        $relative_result = (object)array_fill_keys(\Schema::getColumnListing('hcevaluation01_relatives'), null);
      }
    }

    if(!$result){
      $result =  (object)array_fill_keys(\Schema::getColumnListing($form), null);
    }
    // 取得住院及結案資訊
    $case_open = $db->table('case_open')->where('caseID', $caseID)->orderByDesc('open_date')->first();
    $case_closed = $db->table('case_closed')->where('caseID', $caseID)->orderByDesc('close_date')->first();
    if($formID=="form01"){
      return view('hcevaluation.'.$formID, compact('the_case', 'result', 'case_open', 'case_closed', 'formID', 'caseID', 'date', 'medical_result', 'relative_result'));
    }else{
      return view('hcevaluation.'.$formID, compact('the_case', 'result', 'case_open', 'case_closed', 'formID', 'caseID', 'date'));
    }
  }

  public function save(Request $request)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    $formID = $request->input('formID');
    $caseID = $request->input('caseID');
    $date = $request->input('date');
    // dd($request->all());
    $photoUrl = $request->input('old_photo_url'); // 預設為舊的 URL
    // 如果有上傳新檔案，就取代原圖
    if ($request->hasFile('photo_url') && $request->file('photo_url')->isValid()) {
      // 刪除舊圖
      if ($request->old_photo_url) {
        $oldPath = str_replace('/storage/', 'public/', $request->old_photo_url);
        Storage::delete($oldPath);
      }

      $file = $request->file('photo_url');
      $folder = 'public/'.session('client_id').'/case_img';
      $filename = 'case_' . $request->caseID . '_' . time() . '.' . $file->getClientOriginalExtension();
      $path = $file->storeAs($folder, $filename);
      $photoUrl = Storage::url($path); // 取得圖片公開路徑
      
      $db->table('cases')
      ->where('caseID', $caseID)
      ->update([
        'photo_url' => $photoUrl
      ]);
    }

    // 分類要存入的資料（可根據你的資料欄位需求再分）
    if($formID=="hcevaluation01"){
      $basicData = $request->only(['caseID', 'name', 'gender', 'birthdate', 'IdNo']);
      // $formData = $request->except(['_token', 'formID', 'name', 'gender', 'old_photo_url', 'photo_url', 'birthdate', 'IdNo']);
      $formData = collect($request->except([
        '_token', 'formID', 'name', 'gender', 'old_photo_url', 'photo_url', 'birthdate', 'IdNo'
      ]))
      ->reject(function ($value, $key) {
          return preg_match('/^form01/', $key);
      })
      ->toArray();
      
      //把 $formData 陣列中所有 null 的欄位都換成空字串（''），其他欄位保持原樣
      $formData = array_map(function ($value) {
        return is_null($value) ? '' : $value;
      }, $formData);

      // 指定多選欄位名稱（可依你表單實際欄位調整），處理多選欄位
      $multiSelectFields = ['DisabilityType', 'MEventItem'];
      foreach ($multiSelectFields as $field) {
        if (isset($formData[$field]) && is_array($formData[$field])) {
          // // 將陣列轉成逗號分隔字串
          // $formData[$field] = implode(',', $formData[$field]);
          // 儲存為 JSON 格式字串
          $formData[$field] = json_encode($formData[$field], JSON_UNESCAPED_UNICODE);
        }
      }

      $db->table('cases')->where('caseID', $caseID)->update($basicData);

      $exists = $db->table($formID)
        ->where('caseID', $caseID)
        ->where('date', $date)
        ->exists();

      if ($exists) {
          // 更新
          $formData['updated_by'] = session('user_id') ?? 'Auto';
          $formData['updated_at'] = date('Y-m-d H:i:s');
      } else {
          // 新增
          $formData['created_by'] = session('user_id') ?? 'Auto';
      }
      $db->table($formID)->updateOrInsert(
        ['caseID' => $caseID, 'date' => $date],
        $formData
      );

      //共照團隊醫事人員，刪除、修改
      $deletedIds = array_filter(array_map('trim', explode(',', (string) $request->input('form01_deleted_ids', ''))));
      foreach ($deletedIds as $id) {
        if (!empty($id)) {
          // $preview['delete'][] = "1. DELETE FROM hcevaluation01_medicals WHERE id = $id";
          $db->table('hcevaluation01_medicals')->where('id', $id)->delete();
        }
      }
      
      $infoNos = array_filter(array_map('trim', explode(',', $request->input('form01_infoNo', ''))));
      $oldNames = $request->input('form01_oldName', []);   
      foreach ($infoNos as $i => $id) {
        if (!isset($oldNames[$id])) continue;
        $name = trim($oldNames[$id]);
        if ($name === '') {
          // 空姓名視為刪除
          $db->table('hcevaluation01_medicals')->where('id', $id)->delete();
          continue;
        }
        $db->table('hcevaluation01_medicals')->where('id', $id)->update([
          'Name' => $request->form01_oldName[$id],
          'CareDate' => $request->form01_oldCareDate[$id]?? '',
          'IdNo' => $request->form01_oldIdNo[$id]?? '',
          'JobTitle' => $request->form01_oldJobTitle[$id]?? '',
          'JobTitleOther' => $request->form01_oldJobTitleOther[$id] ?? '',
          'Tel' => $request->form01_oldTel[$id]?? '',
          'CareRemark' => $request->form01_oldCareRemark[$id]?? '',
          'updated_by' => session('user_id'),
          'updated_at' => now()
        ]);
      }
     
      //共照團隊醫事人員，新增
      $names = $request->input('form01Name', []);
      $careDates = $request->input('form01CareDate', []);
      $idNos = $request->input('form01IdNo', []);
      $jobTitles = $request->input('form01JobTitle', []);
      $jobTitleOthers = $request->input('form01JobTitleOther', []);
      $tels = $request->input('form01Tel', []);
      $remarks = $request->input('form01CareRemark', []);

      foreach ($names as $i => $name) {
        if (trim($name) === '') continue; // 跳過空白姓名
        $db->table('hcevaluation01_medicals')->insert([
          'caseID'         => $caseID,
          'date'           => $date,
          'Name'           => $name,
          'CareDate'       => $careDates[$i] ?? '0000-00-00',
          'IdNo'           => $idNos[$i] ?? '',
          'JobTitle'       => $jobTitles[$i] ?? '',
          'JobTitleOther'  => $jobTitleOthers[$i] ?? '',
          'Tel'            => $tels[$i] ?? '',
          'CareRemark'     => $remarks[$i] ?? '',
          'created_by'     => session('user_id'),
          'created_at'     => now()
        ]);
      }
    }

    return redirect()->back()->with('success', '資料已成功儲存');
  }
}