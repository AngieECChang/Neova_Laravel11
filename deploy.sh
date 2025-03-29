
#!/bin/bash  

echo "🚀 開始部署 Laravel 專案..."

# 進入專案資料夾（請依實際路徑調整）
cd /var/www/your-laravel-project || exit

# 拉最新程式碼
echo "📥 拉取 Git 最新程式碼..."
git pull origin main

# 安裝 Composer 套件
echo "📦 安裝 Composer 套件..."
composer install --no-dev --optimize-autoloader

# 清除並快取 config、route、view
echo "🔧 優化 Laravel 快取..."
php artisan config:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

# 建立 storage 的 symbolic link
echo "🔗 建立 public/storage 連結..."
php artisan storage:link

# 執行資料庫 migration（如果有需要）
echo "🗂️  執行 migrate..."
php artisan migrate --force

# 設定正確的檔案權限（依照你環境決定是否執行）
echo "🔒 設定權限..."
chown -R www-data:www-data .
chmod -R ug+rwx storage bootstrap/cache

echo "✅ 部署完成！"
