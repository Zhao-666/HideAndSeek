<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/3/16
 * Time: 18:55
 */

require_once __DIR__ . "/vendor/autoload.php";

$redId = "red_player";
$blueId = "blue_player";
$roomId = "room_id_1";

$global = [];

//创建游戏控制器
$global['rooms'][$roomId]['game'] = new \App\Manager\Game();
//添加玩家
$global['rooms'][$roomId]['game']->createPlayer($redId);
//添加玩家
$global['rooms'][$roomId]['game']->createPlayer($blueId);
//红玩家向左移动
$global['rooms'][$roomId]['game']->playerMove($redId, \App\Model\Player::LEFT);

(new \App\Model\Map(10,10))->printMap();