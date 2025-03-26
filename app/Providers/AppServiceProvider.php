<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\File;

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
        //
    }
}
