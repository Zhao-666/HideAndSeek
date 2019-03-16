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
        if (!empty($this->players)) {
            $player->setColor('#00f');
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
}