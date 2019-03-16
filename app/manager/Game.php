<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/3/16
 * Time: 18:54
 */

namespace app;


class Game
{
    private $mapWidth = 20;
    private $mapHeight = 20;

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
}