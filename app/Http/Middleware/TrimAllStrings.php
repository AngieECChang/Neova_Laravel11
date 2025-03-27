<?php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class TrimAllStrings
{
  public function handle(Request $request, Closure $next)
  {
    $trimmed = array_map(function ($value) {
      return is_string($value) ? trim($value) : $value;
    }, $request->all());

    $request->merge($trimmed);

    return $next($request);
  }
}