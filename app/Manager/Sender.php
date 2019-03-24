<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/3/24
 * Time: 9:51
 */

namespace App\Manager;


class Sender
{
    const MSG_SUCCESS = 1000;

    const CODE_MSG = [
        self::MSG_SUCCESS => 'success'
    ];

    public static function sendMessage($playerId, $code, $data = [])
    {
        $message = [
            'code' => $code,
            'msg' => self::CODE_MSG[$code] ?? '',
            'data' => $data
        ];
        $playerFd = DataCenter::getPlayerFd($playerId);
        if (empty($playerFd)) {
            return;
        }
        DataCenter::$server->push($playerFd, json_encode($message));
    }
}