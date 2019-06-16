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
    const PLAYER_DISPLAY_LEN = 2;

    const GAME_TIME_LIMIT = 10;

    public function acceptChallenge($challengerId, $playerId)
    {
        $this->createRoom($challengerId, $playerId);
    }

    public function refuseChallenge($challengerId)
    {
        Sender::sendMessage($challengerId, Sender::MSG_REFUSE_CHALLENGE);
    }

    public function makeChallenge($opponentId, $playerId)
    {
        if (empty(DataCenter::getOnlinePlayer($opponentId))) {
            Sender::sendMessage($playerId, Sender::MSG_OPPONENT_OFFLINE);
        } else {
            $data = [
                'challenger_id' => $playerId
            ];
            Sender::sendMessage($opponentId, Sender::MSG_MAKE_CHALLENGE, $data);
        }
    }

    public function matchPlayer($playerId)
    {
        //将用户放入队列中
        DataCenter::pushPlayerToWaitList($playerId);
        //发起一个Task尝试匹配
        DataCenter::$server->task(['code' => TaskManager::TASK_CODE_FIND_PLAYER]);
    }

    public function movePlayer($direction, $playerId)
    {
        if (!in_array($direction, Player::DIRECTION)) {
            echo $direction;
            return;
        }
        $roomId = DataCenter::getPlayerRoomId($playerId);
        if (isset(DataCenter::$global['rooms'][$roomId])) {
            /**
             * @var Game $gameManager
             */
            $gameManager = DataCenter::$global['rooms'][$roomId]['manager'];
            $gameManager->playerMove($playerId, $direction);
            $this->sendGameInfo($roomId);
            $this->checkGameOver($roomId);
        }
    }

    public function createRoom($redPlayer, $bluePlayer)
    {
        $roomId = uniqid('room_');
        $this->bindRoomWorker($redPlayer, $roomId);
        $this->bindRoomWorker($bluePlayer, $roomId);
    }

    public function closeRoom($closerId)
    {
        $roomId = DataCenter::getPlayerRoomId($closerId);
        if (!empty($roomId)) {
            /**
             * @var Game $gameManager
             * @var Player $player
             */
            $gameManager = DataCenter::$global['rooms'][$roomId]['manager'];
            $players = $gameManager->getPlayers();
            foreach ($players as $player) {
                if ($player->getId() != $closerId) {
                    Sender::sendMessage($player->getId(), Sender::MSG_OTHER_CLOSE);
                }
                DataCenter::delPlayerRoomId($player->getId());
            }
            unset(DataCenter::$global['rooms'][$roomId]);
        }
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
            DataCenter::$global['rooms'][$roomId]['timer_id'] = $this->createGameTimer($roomId);
            Sender::sendMessage($playerId, Sender::MSG_ROOM_START);
            $this->sendGameInfo($roomId);
        }
    }

    private function createGameTimer($roomId)
    {
        return swoole_timer_after(self::GAME_TIME_LIMIT * 1000, function () use ($roomId) {
            if (isset(DataCenter::$global['rooms'][$roomId])) {
                //游戏还未结束则主动结束游戏
                /**
                 * @var Game $gameManager
                 */
                $gameManager = DataCenter::$global['rooms'][$roomId]['manager'];
                $players = $gameManager->getPlayers();
                $winner = end($players)->getId();
                $this->gameOver($roomId, $winner);
            }
        });
    }

    private function checkGameOver($roomId)
    {
        /**
         * @var Game $gameManager
         * @var Player $player
         */
        $gameManager = DataCenter::$global['rooms'][$roomId]['manager'];
        if ($gameManager->isGameOver()) {
            $players = $gameManager->getPlayers();
            $winner = current($players)->getId();
            $this->gameOver($roomId, $winner);
        }
    }

    private function gameOver($roomId, $winner)
    {
        /**
         * @var Game $gameManager
         * @var Player $player
         */
        $gameManager = DataCenter::$global['rooms'][$roomId]['manager'];
        $players = $gameManager->getPlayers();
        DataCenter::addPlayerWinTimes($winner);
        foreach ($players as $player) {
            Sender::sendMessage($player->getId(), Sender::MSG_GAME_OVER, ['winner' => $winner]);
            DataCenter::delPlayerRoomId($player->getId());
        }
        unset(DataCenter::$global['rooms'][$roomId]);
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
        //必须倒序输出，因为游戏设定数组第一个是寻找者，第二个是躲藏者，叠加时赢的是寻找者。
        foreach (array_reverse($players) as $player) {
            $mapData[$player->getX()][$player->getY()] = $player->getId();
        }
        foreach ($players as $player) {
            $data = [
                'players' => $players,
                'map_data' => $this->getNearMap($mapData, $player->getX(), $player->getY()),
                'time_limit' => self::GAME_TIME_LIMIT
            ];
            Sender::sendMessage($player->getId(), Sender::MSG_GAME_INFO, $data);
        }
    }

    private function getNearMap($mapData, $x, $y)
    {
        $result = [];
        for ($i = -1 * self::PLAYER_DISPLAY_LEN; $i <= self::PLAYER_DISPLAY_LEN; $i++) {
            $tmp = [];
            for ($j = -1 * self::PLAYER_DISPLAY_LEN; $j <= self::PLAYER_DISPLAY_LEN; $j++) {
                $tmp[] = $mapData[$x + $i][$y + $j] ?? 0;
            }
            $result[] = $tmp;
        }
        return $result;
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
        DataCenter::setPlayerRoomId($playerId, $roomId);
        Sender::sendMessage($playerId, Sender::MSG_ROOM_ID, ['room_id' => $roomId]);
    }
}