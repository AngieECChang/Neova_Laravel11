<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AddressController;

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
	}
}
