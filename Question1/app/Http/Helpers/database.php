<?php

if (!function_exists('change_db_strict')) {

    /**
     * 啟用或關閉資料庫嚴格模式
     *
     * @param bool $strict
     * @throws Exception
     */
    function change_db_strict(bool $strict)
    {
        $driver = DB::connection()->getDriverName();
        if (!$strict && DB::connection()->transactionLevel() > 0) {
            throw new \Exception ('transaction model can not change strict');
        }

        Config::set("database.connections.{$driver}.strict", $strict);
        DB::reconnect();
    }
}
