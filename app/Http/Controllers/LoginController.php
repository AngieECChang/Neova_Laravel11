<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class LoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function processLogin(Request $request)
    {
      $request->validate([
          'user_id' => 'required',
          'password' => 'required',
      ]);

      // 從 users 資料表查找對應的 user_id
      $user = DB::selectOne("SELECT * FROM users WHERE user_id = ?", [$request->user_id]);
    
      if(!isset($user)){
        return redirect()->route('login')->with('error', '沒有該使用者ID');
      }elseif($user && isset($user->password) && password_verify($request->password, $user->password)){
        $client = DB::selectOne("SELECT * FROM client_info WHERE client_id = ?", [$user->client_id]);
        if(isset($client)){
           session([
            'user_id'   => $user->user_id,
            'name'      => $user->name,
            'client_id' => $user->client_id,
            'client_name' => $client->client_name,
            'client_type' => $client->client_type,
            'DB' => $client->client_db
          ]);
          return redirect()->route('dashboard');
        }else{
          return redirect()->route('login')->with('error', '沒有對應的機構');
        }
      }else{
        return redirect()->route('login')->with('error', '密碼錯誤');
      }
    }

    public function logout()
    {
        session()->flush(); // 清除 Session
        return redirect()->route('login');
    }
}
