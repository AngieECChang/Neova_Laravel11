<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
  public function getTowns(Request $request)
  {
    $city = $request->input('city');
    // $field = $request->input('field', 'city');  // 預設是 'city'
    // $city = $request->input($field);
    
    if ($city === '桃園縣') {
        $city = '桃園市';
    }

    $towns = DB::table('mohw_areacode')
      ->where('city_name', $city)
      ->distinct()
      ->pluck('area_name');

    return response()->json($towns);
  }
}