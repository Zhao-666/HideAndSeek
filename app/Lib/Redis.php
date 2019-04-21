<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/3/17
 * Time: 17:23
 */

namespace App\Lib;


class Redis
{
    protected static $instance;
    protected static $config = [
        'host' => '127.0.0.1',
        'port' => 6379,
    ];

    /**
     * 获取redis实例
     *
     * @return \Redis|\RedisCluster
     */
    public static function getInstance()
    {
        if (empty(self::$instance)) {
            $instance = new \Redis();
            $instance->connect(
                self::$config['host'],
                self::$config['port']
            );
            self::$instance = $instance;
        }
        return self::$instance;
    }
}