<?php

namespace Cut;

class DbConnect
{
    private static $handler = null;
    private static $connectStr = [
        'dsn' => 'mysql:dbname=shortLinks;host=localhost',
        'username' => 'root',
        'password' => '123',
    ];


    /**
     * @throws DbException
     */
    private static function init()
    {
        try {
            self::$handler = new \PDO(
                self::$connectStr["dsn"],
                self::$connectStr['username'],
                self::$connectStr['password']
            );
        } catch (\Exception $e) {
            echo $e->getMessage() . $e->getCode();
        }
    }

    /**
     * @return $handler
     */
    public static function getConnection()
    {
        if (!isset(self::$handler)) {
            self::init();
        }
        return self::$handler;
    }

    public static function disconnect()
    {
        self::$handler = null;
    }

}