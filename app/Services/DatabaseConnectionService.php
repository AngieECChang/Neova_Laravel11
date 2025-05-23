<?php
namespace App\Services;

use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\DB;

class DatabaseConnectionService
{
    private static $initializedConnections = [];

    public static function setConnection($databaseName, $port = 3306)
    {
        $connectionName = $databaseName; // 確保名稱唯一
        $host = '127.0.0.1';
        $username = 'neova';
        $password = 'neova@1234';

        if (in_array($connectionName, self::$initializedConnections)) {
            return DB::connection($connectionName);
        }
        // 檢查是否已經建立該連線，避免重複建立
        if (!array_key_exists($connectionName, Config::get('database.connections'))) {
            Config::set("database.connections.$connectionName", [
              'driver'    => 'mysql',
              'host'      => $host,
              'port'      => $port,
              'database'  => $databaseName,
              'username'  => $username,
              'password'  => $password,
              'charset'   => 'utf8mb4',
              'collation' => 'utf8mb4_general_ci',
              'prefix'    => '',
              'strict'    => false,
              'engine'    => null,
            ]);
        }
        self::$initializedConnections[] = $connectionName;
        
        return DB::connection($connectionName);
    }
}