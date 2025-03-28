<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use App\Http\Controllers\AddressController;
use App\Services\DatabaseConnectionService;

class AppServiceProvider extends ServiceProvider
{
	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		// 自動載入 app/Helpers/ 目錄下所有 PHP 檔案
		foreach (File::glob(app_path('Helpers') . '/*.php') as $filename) {
			require_once $filename;
		}
	}

	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		// 註冊 API 路由（讓 Blade 前端能 AJAX 使用）
		Route::middleware('web')->get('/api/towns', [AddressController::class, 'getTowns']);

		View::composer('*', function ($view) {
			$selected_id = request()->input('employeeID');
			$databaseName = session('DB');
			$db = DatabaseConnectionService::setConnection($databaseName);

			$employees  = $db->table('employees')
      ->whereNotNull('IdNo')
      ->where('IdNo', '!=', '')
      ->where('status', 1)
      ->orderBy('employeeNo')
      ->orderBy('employeeID')
      ->get();

			// 檢查選取的 employee 是否是離職的
			$selected_quit = null;
			if ($selected_id) {
				$selected_quit = $db->table('employees')
					->where('employeeID', $selected_id)
					->where('status', 0)
					->whereNotNull('IdNo')
					->where('IdNo', '!=', '')
					->first();
			}

			View::share('employeesListByIdNo', $employees);
			View::share('selected_employeesByIdNo_quit', $selected_quit);
			View::share('selected_employeesByIdNo_id', $selected_id);
		});
	}
}
