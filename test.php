<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/3/28
 * Time: 22:04
 */

require_once __DIR__ . '/vendor/autoload.php';

use App\Manager\Game;

$redId = "red_player";
$blueId = "blue_player";

//创建游戏控制器
$game = new Game();
//添加玩家
$game->createPlayer($redId, 6, 1);
//添加玩家
$game->createPlayer($blueId, 6, 10);
//移动坐标
$game->playerMove($redId, 'up');
$game->playerMove($redId, 'up');
$game->playerMove($redId, 'up');
//打印地图
$game->printGameMap();