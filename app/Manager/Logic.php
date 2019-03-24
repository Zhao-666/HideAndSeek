<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/3/17
 * Time: 16:59
 */

namespace App\Manager;


class Logic
{
    public function matchPlayer($playerId)
    {
        DataCenter::pushPlayerToWaitList($playerId);
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