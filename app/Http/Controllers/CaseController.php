<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\DatabaseConnectionService;
use Illuminate\Support\Facades\Log;

class CaseController extends Controller
{
  public function case_update(Request $request, $id)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    $currentCase = $db->table('cases')
      ->where('caseID', $id)
      ->select(
        'caseNoDisplay',
        'case_type'
      )
      ->first();

    if (!$currentCase) {
      return response()->json(['success' => false, 'message' => '個案不存在']);
    }

    $newData = [
      'caseNoDisplay' => $request->caseNo,
      'case_type' => $request->caseType
    ];

    $oldData = [
      'caseNoDisplay' => $currentCase->caseNoDisplay,
      'case_type' => $currentCase->case_type
    ];

    if ($newData == $oldData) {
      return response()->json(['success' => false, 'message' => '資料無變更']);
    }else{
      if ($newData['caseNoDisplay'] != $oldData['caseNoDisplay']) {
        $db->table('case_log')
        ->insert([
          'caseID' => $id,
          'date' => now(),
          'action' => 'update',
          'function' => 'change_caseNoDisplay',
          'old_value' => $oldData['caseNoDisplay'],
          'new_value' => $newData['caseNoDisplay'],
          'filler' => session('user_id')
        ]);
      }
      if ($newData['case_type'] != $oldData['case_type']) {
        $db->table('case_log')
        ->insert([
          'caseID' => $id,
          'date' => now(),
          'action' => 'update',
          'function' => 'change_caseType',
          'old_value' => $oldData['case_type'],
          'new_value' => $newData['case_type'],
          'filler' => session('user_id')
        ]);
      }

      $affected = $db->table('cases')
      ->where('caseID', $id)
      ->update([
        'caseNoDisplay' => $request->caseNo,
        'case_type' => $request->caseType
      ]);
  
      if ($affected) {
        return response()->json(['success' => true, 'message' => '更新成功']);
      } else {
        return response()->json(['success' => false, 'message' => '更新失敗']);
      }
    }
  }

  public function case_new(Request $request)
  {
    try {
      $databaseName = session('DB'); // 可根據條件動態變更
      $db = DatabaseConnectionService::setConnection($databaseName);

      // 驗證輸入資料
      $request->validate([
          'name' => 'required|string|max:100',
          'id_number' => 'required|string|size:10',
          'birthday' => 'required|regex:/^\d{2,3}\/\d{1,2}\/\d{1,2}$/', // ROC 年/月/日 格式
          'case_type' => 'required|string',
          'case_no' => 'nullable|string|max:20',
      ], [
          'birthday.regex' => '生日格式錯誤，請輸入民國年/月/日，例如：112/03/18。',
      ]);

      // 轉換民國年為西元年
      $rocDate = $request->input('birthday'); // 112/03/18
      if (preg_match('/^(\d{2,3})\/(\d{1,2})\/(\d{1,2})$/', $rocDate, $matches)) {
          $year = (int)$matches[1] + 1911;
          $formattedDate = "{$year}-{$matches[2]}-{$matches[3]}"; // 轉換為 2023-03-18
      } else {
          return response()->json(['success' => false, 'message' => '生日格式錯誤'], 400);
      }

      // 插入資料到case並取到剛剛插入的那筆資料的主鍵 ID，caseID 欄位必須是該資料表的主鍵
      $caseID = $db->table('cases')->insertGetId([
        'name' => $request->input('name'),
        'gender' => $request->input('gender'),
        'IdNo' => $request->input('id_number'),
        'birthdate' => $formattedDate, // 存入西元年
        'case_type' => $request->input('case_type'),
        'caseNoDisplay' => $request->input('case_no'),
        'user' => session('user_id')
      ]);

      $update = $db->table('cases')
        ->where('caseID', $caseID)
        ->update([
          'caseNo' => $caseID
        ]);

      $db->table('case_open')->insert([
        'caseID' => $caseID,
        'bedID' => $caseID,
        'open_date' => $request->input('open_date'),
        'user' => session('user_id')
      ]);

      $db->table('bed_info')->insert([
        'bedID' => $caseID,
        'areaID' => $request->input('area')
      ]);

      $db->table('case_log')->insert([
        'caseID' => $caseID,
        'date' => now(),
        'action' => 'insert',
        'function' => 'insert_bed',
        'old_value' => '',
        'new_value' => $caseID,
        'filler' => session('user_id')
      ]);

      return response()->json(['success' => true]);
    }
    catch (\Exception $e) {
      \Log::error('個案新增失敗：' . $e->getMessage());
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }

  public function checkIdNumber(Request $request)
  {
    $idNumber = strtoupper($request->input('id_number'));

    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    try {
      $exists = $db->table('cases')
      ->where(DB::raw('UPPER(IdNo)'), strtoupper($idNumber))
      ->exists();
      
      return response()->json(['id_number' => $idNumber,'exists' => $exists]);
    } catch (\Exception $e) {
      // 如果真的錯了，把錯誤丟回去看訊息
      return response()->json(['success' => false,'message' => '伺服器錯誤：' . $e->getMessage()], 500);
    }
  }

  public function case_close(Request $request, $id)
  {
    try {
      $databaseName = session('DB'); // 可根據條件動態變更
      $db = DatabaseConnectionService::setConnection($databaseName);

      $db->table('case_closed')->insert([
        'caseID' => $id,
        'open_date' => $request->input('opendate'),
        'close_date' => $request->input('closeDate'),
        'reason' => $request->input('closeReason'), 
        'memo' => $request->input('closeReasonOther'),
        'areaID' => $request->input('caseArea'),
        'bedID' => $id,
        'type' => $request->input('caseType'),
        'user' => session('user_id')
      ]);

      $db->table('case_open')
        ->where([
          ['caseID', '=', $id],
          ['open_date', '=', $request->input('opendate')]
        ])
        ->delete();

      $db->table('bed_info')
        ->where('bedID', $id)
        ->delete();

      return response()->json(['success' => true]);
    }
    catch (\Exception $e) {
      \Log::error('個案結案失敗：' . $e->getMessage());
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }
}