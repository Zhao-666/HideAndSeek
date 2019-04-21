<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/4/20
 * Time: 13:52
 */

namespace App\Manager;

class Logic
{
    public function matchPlayer($playerId)
    {
        //将用户放入队列中
        DataCenter::pushPlayerToWaitList($playerId);
        //发起一个Task尝试匹配
        DataCenter::$server->task(['code' => TaskManager::TASK_CODE_FIND_PLAYER]);
    }

    public function createRoom($redPlayer, $bluePlayer)
    {
        $roomId = uniqid('room_');
        $this->bindRoomWorker($redPlayer, $roomId);
        $this->bindRoomWorker($bluePlayer, $roomId);
    }

    private function bindRoomWorker($playerId, $roomId)
    {
        $playerFd = DataCenter::getPlayerFd($playerId);
        DataCenter::$server->bind($playerFd, crc32($roomId));
        Sender::sendMessage($playerId, Sender::MSG_ROOM_ID, ['room_id' => $roomId]);
    }
}