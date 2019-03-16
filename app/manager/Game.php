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
    private $mapWidth = 10;
    private $mapHeight = 10;

    private $gameMap = [];
    private $players = [];

    public function __construct()
    {
        $this->gameMap = new Map($this->mapWidth, $this->mapHeight);
    }

    public function createPlayer($playerId)
    {
        $player = new Player($playerId);
        $player->setDirection(5, 1);
        if (!empty($this->players)) {
            $player->setColor('#00f');
            $player->setDirection(6, 10);
        }
        $this->players[$playerId] = $player;
    }

    public function playerMove($playerId, $direction)
    {
        $this->players[$playerId]->{$direction}();
    }

    public function getGameData()
    {
        return [
            'players' => $this->players,
            'map' => $this->gameMap->getMapData()
        ];
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
}