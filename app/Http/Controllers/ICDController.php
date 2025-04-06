<?php
namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Cache;
use App\Services\DatabaseConnectionService;

class ICDController extends Controller
{
  public function lookup(Request $request)
  {
    $db = DatabaseConnectionService::setConnection('nvhcs-1');
    $query = $request->get('q');
    $querySafe = addslashes($query);
    $key = 'icd_lookup_' . md5($query);

    $results = Cache::remember($key, now()->addHours(2), function () use ($querySafe, $db) {
        return $db->table('icd9to10 as a')
            ->join($db->raw('(
                SELECT MIN(id) as id
                FROM icd9to10
                WHERE icd10_new LIKE "' . $querySafe . '%"
                   OR icd10_cname LIKE "%' . $querySafe . '%"
                GROUP BY icd10_new
                LIMIT 10
            ) as b'), 'a.id', '=', 'b.id')
            ->select('a.icd10_new', 'a.icd10_cname')
            ->orderBy('a.icd10_new')
            ->get();
    });

    return response()->json($results);
  }
}
