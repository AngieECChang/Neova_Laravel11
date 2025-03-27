<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AddressController extends Controller
{
  public function getTowns(Request $request)
  {
    $city = $request->input('city');
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