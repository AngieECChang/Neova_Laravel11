<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;

class GlobalDataMiddleware
{
  public function handle(Request $request, Closure $next)
  {
    // 取得目前登入的使用者
    $client = session('client_id');
    $user = session('user_id');
    if($client && $user){
    $permissions = DB::table('user_permission as a')
      ->leftJoin('permission_item as b', 'a.itemID', '=', 'b.itemID')
      ->join('permission_subcate as c', 'b.subcateID', '=', 'c.subcateID')
      ->join('permission_cate as d', 'c.cateID', '=', 'd.cateID')
      ->where('a.client_id', $client)
      ->where('a.user_id', $user)
      ->select(
          'a.*',
          'b.*',
          'c.name as subcate_name',
          'c.icon as subcate_icon',
          'd.cateID',
          'd.cate_shortname'
      )
      ->orderBy('d.order')
      ->orderBy('c.order')
      ->orderBy('b.order')
      ->get()
      ->groupBy('cateID'); // 以 cateID 分類

      // 共享數據到所有視圖
      View::share('allow_permission', $permissions);
      // dd($permissions->toArray()); // 測試 Middleware 是否正常運作
    }

    return $next($request);
  }
}
