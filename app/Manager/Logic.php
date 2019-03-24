<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/3/17
 * Time: 16:59
 */

namespace App\Manager;


use App\Model\Player;

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

    public function startRoom($roomId, $playerId)
    {
        if (!isset(DataCenter::$global['rooms'][$roomId])) {
            DataCenter::$global['rooms'][$roomId] = [
                'id' => $roomId,
                'manager' => new Game()
            ];
        }
        /**
         * @var Game $gameManager
         */
        $gameManager = DataCenter::$global['rooms'][$roomId]['manager'];
        if (empty(count($gameManager->getPlayers()))) {
            //第一个玩家
            $gameManager->createPlayer($playerId, 6, 1);
            Sender::sendMessage($playerId, Sender::MSG_WAIT_PLAYER);
        } else {
            //第二个玩家
            $gameManager->createPlayer($playerId, 6, 10);
            Sender::sendMessage($playerId, Sender::MSG_ROOM_START);
            $this->sendGameInfo($roomId);
        }
    }

    private function sendGameInfo($roomId)
    {
        /**
         * @var Game $gameManager
         * @var Player $player
         */
        $gameManager = DataCenter::$global['rooms'][$roomId]['manager'];
        $players = $gameManager->getPlayers();
        $mapData = $gameManager->getMapData();
        foreach ($players as $player) {
            $mapData[$player->getX()][$player->getY()] = $player->getId();
        }
        foreach ($players as $player) {
            $data = [
                'players' => $players,
                'map_data' => $this->getNearMap($mapData, $player->getX(), $player->getY())
            ];
            Sender::sendMessage($player->getId(), Sender::MSG_GAME_INFO, $data);
        }
    }

    private function getNearMap($mapData, $x, $y)
    {
        return [
            [$mapData[$x - 1][$y - 1], $mapData[$x - 1][$y], $mapData[$x - 1][$y + 1]],
            [$mapData[$x][$y - 1], $mapData[$x][$y], $mapData[$x][$y + 1]],
            [$mapData[$x + 1][$y - 1], $mapData[$x + 1][$y], $mapData[$x + 1][$y + 1]]
        ];
    }

    /**
     * 绑定同一个room的player到某个worker进程中，内存共享
     * @param $playerId
     * @param $roomId
     */
    private function bindRoomWorker($playerId, $roomId)
    {
        $playerFd = DataCenter::getPlayerFd($playerId);
        DataCenter::$server->bind($playerFd, crc32($roomId));
        Sender::sendMessage($playerId, Sender::MSG_ROOM_ID, ['room_id' => $roomId]);
    }
}