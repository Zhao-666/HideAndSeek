<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/3/24
 * Time: 11:50
 */

namespace App\Manager;


class TaskManager
{
    const TASK_CODE_FIND_PLAYER = 1;

    public static function findPlayer()
    {
        $playerListLen = DataCenter::getPlayerWaitListLen();
        if ($playerListLen >= 2) {
            $redPlayer = DataCenter::popPlayerFromWaitList();
            $bluePlayer = DataCenter::popPlayerFromWaitList();
            return [
                'red_player' => $redPlayer,
                'blue_player' => $bluePlayer
            ];
        }
        return false;
    }
}