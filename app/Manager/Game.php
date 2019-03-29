<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/3/28
 * Time: 22:27
 */

namespace App\Manager;

use App\Model\Map;

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

    }

    public function playerMove($playerId, $direction)
    {

    }
}