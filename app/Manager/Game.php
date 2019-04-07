<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/3/28
 * Time: 22:27
 */

namespace App\Manager;

use App\Model\Map;
use App\Model\Player;

class Game
{

    private $gameMap = [];
    private $players = [];

    public function __construct()
    {
        $this->gameMap = new Map(12, 12);
    }

    public function createPlayer($playerId, $x, $y)
    {
        $player = new Player($playerId, $x, $y);
        if (!empty($this->players)) {
            $player->setType(Player::PLAYER_TYPE_HIDE);
        }
        $this->players[$playerId] = $player;
    }

    public function playerMove($playerId, $direction)
    {
        $player = $this->players[$playerId];
        if ($this->canMoveToDirection($player, $direction)) {
            $player->{$direction}();
        }
    }

    public function printGameMap()
    {
        $mapData = $this->gameMap->getMapData();
        $font = [2 => '追', 3 => '躲'];
        /* @var Player $player */
        foreach ($this->players as $player) {
            $mapData[$player->getX()][$player->getY()] = $player->getType() + 1;
        }
        foreach ($mapData as $line) {
            foreach ($line as $item) {
                if (empty($item)) {
                    echo "墙，";
                } elseif ($item == 1) {
                    echo "    ";
                } else {
                    echo $font[$item] . '，';
                }
            }
            echo PHP_EOL;
        }
    }

    /**
     * @param Player $player
     * @param $direction
     * @return bool
     */
    private function canMoveToDirection($player, $direction)
    {

    }
}