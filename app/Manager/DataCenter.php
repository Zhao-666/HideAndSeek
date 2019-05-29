<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/4/20
 * Time: 13:53
 */

namespace App\Manager;

use App\Lib\Redis;

class DataCenter
{
    const PREFIX_KEY = "game";

    public static $server;
    public static $global;

    public static function redis()
    {
        return Redis::getInstance();
    }

    public static function setOnlinePlayer($playerId)
    {
        $key = self::PREFIX_KEY . ':online_player';
        self::redis()->hSet($key, $playerId, 1);
    }

    public static function getOnlinePlayer($playerId)
    {
        $key = self::PREFIX_KEY . ':online_player';
        return self::redis()->hGet($key, $playerId);
    }

    public static function delOnlinePlayer($playerId)
    {
        $key = self::PREFIX_KEY . ':online_player';
        self::redis()->hDel($key, $playerId);
    }

    public static function lenOnlinePlayer()
    {
        $key = self::PREFIX_KEY . ':online_player';
        return self::redis()->hLen($key);
    }

    public static function setPlayerRoomId($playerId, $roomId)
    {
        $key = self::PREFIX_KEY . ':player_room_id:' . $playerId;
        self::redis()->set($key, $roomId);
    }

    public static function getPlayerRoomId($playerId)
    {
        $key = self::PREFIX_KEY . ':player_room_id:' . $playerId;
        return self::redis()->get($key);
    }

    public static function delPlayerRoomId($playerId)
    {
        $key = self::PREFIX_KEY . ':player_room_id:' . $playerId;
        self::redis()->del($key);
    }

    public static function getPlayerWaitListLen()
    {
        $key = self::PREFIX_KEY . ":player_wait_list";
        return self::redis()->lLen($key);
    }

    public static function pushPlayerToWaitList($playerId)
    {
        $key = self::PREFIX_KEY . ":player_wait_list";
        self::redis()->lPush($key, $playerId);
    }

    public static function popPlayerFromWaitList()
    {
        $key = self::PREFIX_KEY . ":player_wait_list";
        return self::redis()->rPop($key);
    }

    public static function getPlayerFd($playerId)
    {
        $key = self::PREFIX_KEY . ":player_fd:" . $playerId;
        return self::redis()->get($key);
    }

    public static function setPlayerFd($playerId, $playerFd)
    {
        $key = self::PREFIX_KEY . ":player_fd:" . $playerId;
        self::redis()->set($key, $playerFd);
    }

    public static function delPlayerFd($playerId)
    {
        $key = self::PREFIX_KEY . ":player_fd:" . $playerId;
        self::redis()->del($key);
    }

    public static function getPlayerId($playerFd)
    {
        $key = self::PREFIX_KEY . ":player_id:" . $playerFd;
        return self::redis()->get($key);
    }

    public static function setPlayerId($playerFd, $playerId)
    {
        $key = self::PREFIX_KEY . ":player_id:" . $playerFd;
        self::redis()->set($key, $playerId);
    }

    public static function delPlayerId($playerFd)
    {
        $key = self::PREFIX_KEY . ":player_id:" . $playerFd;
        self::redis()->del($key);
    }

    public static function setPlayerInfo($playerId, $playerFd)
    {
        self::setPlayerId($playerFd, $playerId);
        self::setPlayerFd($playerId, $playerFd);
        self::setOnlinePlayer($playerId);
    }

    public static function delPlayerInfo($playerFd)
    {
        $playerId = self::getPlayerId($playerFd);
        self::delPlayerFd($playerId);
        self::delPlayerId($playerFd);
        self::delOnlinePlayer($playerId);
    }

    public static function cleanRoomData($roomId)
    {
        if (isset(self::$global['rooms'][$roomId])) {
            unset(self::$global['rooms'][$roomId]);
        }
    }

    public static function initDataCenter()
    {
        //清空匹配队列
        $key = self::PREFIX_KEY . ':player_wait_list';
        self::redis()->del($key);
        //清空玩家ID
        $key = self::PREFIX_KEY . ':player_id*';
        $values = self::redis()->keys($key);
        foreach ($values as $value) {
            self::redis()->del($value);
        }
        //清空玩家FD
        $key = self::PREFIX_KEY . ':player_fd*';
        $values = self::redis()->keys($key);
        foreach ($values as $value) {
            self::redis()->del($value);
        }
        //清空在线玩家
        $key = self::PREFIX_KEY . ':online_player';
        self::redis()->del($key);
    }

    public static function log($info, $context = [], $level = 'INFO')
    {
        if ($context) {
            echo sprintf("[%s][%s]: %s %s\n", date('Y-m-d H:i:s'), $level, $info, json_encode($context, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE));
        } else {
            echo sprintf("[%s][%s]: %s\n", date('Y-m-d H:i:s'), $level, $info);
        }
    }
}