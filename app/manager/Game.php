<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/3/16
 * Time: 18:54
 */

namespace App\Manager;


use App\Model\Map;
use App\Model\Player;

class Game
{
    private $mapWidth = 12;
    private $mapHeight = 12;

    private $gameMap = [];
    private $players = [];

    public function __construct()
    {
        $this->gameMap = new Map($this->mapWidth, $this->mapHeight);
    }

    public function createPlayer($playerId)
    {
        $player = new Player($playerId);
        $player->setCoordinate(5, 1);
        if (!empty($this->players)) {
            $player->setColor('#00f');
            $player->setCoordinate(6, 10);
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
        $font = [2 => '红', 3 => '蓝'];
        $index = 2;
        foreach ($this->players as $player) {
            /**
             * @var Player $player
             */
            $mapData[$player->getX()][$player->getY()] = $index++;
        }
        foreach ($mapData as $column) {
            foreach ($column as $item) {
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