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

    public function isGameOver()
    {
        $result = false;
        $x = -1;
        $y = -1;
        $players = array_values($this->players);
        /* @var Player $player */
        foreach ($players as $key => $player) {
            if ($key == 0) {
                $x = $player->getX();
                $y = $player->getY();
            } elseif ($x == $player->getX() && $y == $player->getY()) {
                $result = true;
            }
        }
        return $result;
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
        $x = $player->getX();
        $y = $player->getY();
        $moveCoor = $this->getMoveCoor($x, $y, $direction);
        $mapData = $this->gameMap->getMapData();
        if (!$mapData[$moveCoor[0]][$moveCoor[1]]) {
            return false;
        }
        return true;
    }

    private function getMoveCoor($x, $y, $direction)
    {
        switch ($direction) {
            case Player::UP:
                return [--$x, $y];
            case Player::DOWN:
                return [++$x, $y];
            case Player::LEFT:
                return [$x, --$y];
            case Player::RIGHT:
                return [$x, ++$y];
        }
        return [$x, $y];
    }
}