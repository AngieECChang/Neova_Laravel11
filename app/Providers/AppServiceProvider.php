<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Schema;
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
		Schema::defaultStringLength(191); // 解決 key 太長的問題
		// 註冊 API 路由（讓 Blade 前端能 AJAX 使用）
		Route::middleware('web')->get('/api/towns', [AddressController::class, 'getTowns']);
	}
}
