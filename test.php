<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/3/16
 * Time: 18:55
 */

$redPlayer = "redPlayer";
$bluePlayer = "bluePlayer";
$roomId = "room_id_1";

$global = [];

$game = new \app\Game();

$global['rooms'][$roomId]['game'] = $game;
$global['rooms'][$roomId]['players'][$redPlayer] = new \app\Player($redPlayer);
$global['rooms'][$roomId]['players'][$bluePlayer] = new \app\Player($bluePlayer);

