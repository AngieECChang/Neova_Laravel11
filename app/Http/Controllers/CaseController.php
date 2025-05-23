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
          'action' => 'case_update',
          'function' => 'caseNoDisplay_change',
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
          'action' => 'case_update',
          'function' => 'caseType_change',
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

  public function case_update_area(Request $request, $id)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    $bed = $request->input('caseBed');
    $newArea = $request->input('caseArea');
    $oldArea = $request->input('caseArea_original');

    if ($newArea == $oldArea) {
      return response()->json(['success' => false, 'message' => '資料無變更']);
    }else{
      
      $db->table('case_log')
      ->insert([
        'caseID' => $id,
        'date' => now(),
        'action' => 'case_update_area',
        'function' => 'caseArea_change',
        'old_value' => $oldArea,
        'new_value' => $newArea,
        'filler' => session('user_id')
      ]);

      $affected = $db->table('bed_info')
      ->where('bedID', $bed)
      ->update([
        'areaID' => $newArea
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
        'function' => 'Bed_insert',
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
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }

  public function reopen_case(Request $request, $id)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    $opendate = $request->input('opendate');
    $closedate = $request->input('closedate');
    $reopenDate = $request->input('reopenDate');
    $reopenArea = $request->input('reopenArea');
    $reopenType = $request->input('reopenType');
    $caseType = $request->input('caseType');
    $closeArea = $request->input('closeArea');
    $closeBed = $request->input('closeBed');

    // 驗證重收日期是否比收案日期和結案日期大
    if (strtotime($reopenDate) <= strtotime($opendate) || strtotime($reopenDate) < strtotime($closedate)) {
      return response()->json(['success' => false, 'message' => '重收日期必須晚於收案日期與結案日期！']);
    }

    // 檢查是否已存在於 case_open，避免重複插入
    $exists = $db->table('case_open')->where('caseID', $id)->exists();

    try {
      if (!$exists) {
        $db->table('case_open')->insert([
          'caseID' => $id,
          'bedID' => $closeBed,
          'open_date' => $reopenDate,
          'user' => session('user_id')
        ]);

        $db->table('bed_info')->insert([
          'bedID' => $closeBed,
          'areaID' => $reopenArea
        ]);

        if ($closeArea != $reopenArea) {
          $db->table('case_log')
          ->insert([
            'caseID' => $id,
            'date' => now(),
            'action' => 'reopen',
            'function' => 'caseArea_change',
            'old_value' => $closeArea,
            'new_value' => $reopenArea,
            'filler' => session('user_id')
          ]);
        }
        if ($caseType != $reopenType) {
          $db->table('cases')
          ->where('caseID', $id)
          ->update([
            'case_type' => $reopenType
          ]);
          $db->table('case_log')
          ->insert([
            'caseID' => $id,
            'date' => now(),
            'action' => 'reopen',
            'function' => 'caseType_change',
            'old_value' => $caseType,
            'new_value' => $reopenType,
            'filler' => session('user_id')
          ]);
        }
        return response()->json(['success' => true, 'message' => '重新收案成功']);
      }
    }
    catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }

  public function delete_close(Request $request, $id)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);
    try {
      // 取得即將刪除的紀錄
      $record = $db->table('case_closed')
        ->where([
            ['caseID', '=', $id],
            ['open_date', '=', $request->input('opendate')],
            ['close_date', '=', $request->input('closedate')]
        ])
        ->first(); // 只取出第一筆符合條件的資料

      if (!$record) {
        return response()->json(['success' => false, 'message' => "找不到符合條件的紀錄，刪除失敗"]);
      }

      // 轉換 JSON，確保不是 null
      $recordJson = $record ? json_encode($record, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT) : '{}';

      // // 執行刪除並取得影響的筆數
      $deletedRows = $db->table('case_closed')
        ->where([
            ['caseID', '=', $id],
            ['open_date', '=', $request->input('opendate')],
            ['close_date', '=', $request->input('closedate')]
        ])
        ->delete();
      // $sql = "DELETE FROM case_closed WHERE caseID = ? AND open_date = ? AND close_date = ?";
      // $result = $db->delete($sql, [$id, $request->input('opendate'), $request->input('closedate')]);

      if ($deletedRows > 0) {
        $db->table('case_log')->insert([
            'caseID' => $id,
            'date' => now(),
            'action' => 'delete',
            'function' => 'closecase_delete',
            'old_value' => $recordJson,
            'new_value' => '',
            'filler' => session('user_id')
        ]);
        return response()->json(['success' => true, 'message' => "刪除成功"]);
      } else {
        return response()->json(['success' => false, 'message' => "刪除失敗，沒有符合的紀錄"]);
      }
    } catch (\Exception $e) {
      return response()->json(['success' => false, 'message' => $e->getMessage()]);
    }
  }
}