<?php
namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Services\DatabaseConnectionService;

class CaseController extends Controller
{
  public function case_update(Request $request, $id)
  {
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    $currentCase = $db->table('case')
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

      $affected = $db->table('case')
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
    $databaseName = session('DB'); // 可根據條件動態變更
    $db = DatabaseConnectionService::setConnection($databaseName);

    // 驗證輸入資料
    $request->validate([
        'name' => 'required|string|max:50',
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

    // 插入資料到 DB
    $db->table('cases')->insert([
      'name' => $request->input('name'),
      'IdNo' => $request->input('id_number'),
      'birthdate' => $formattedDate, // 存入西元年
      'case_type' => $request->input('case_type'),
      'caseNo' => $request->input('case_no')
    ]);

    return response()->json(['success' => true]);
  }
}