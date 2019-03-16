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

//创建游戏控制器
$game = new \App\Manager\Game();
//添加玩家
$game->createPlayer($redId);
//添加玩家
$game->createPlayer($blueId);

$game->printGameMap();
//红玩家向右移动
$game->playerMove($redId, \App\Model\Player::RIGHT);

$game->printGameMap();