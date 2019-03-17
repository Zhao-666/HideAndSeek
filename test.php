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
$game->createPlayer($redId, 6, 1);
//添加玩家
$game->createPlayer($blueId, 6, 10);
//打印初始战局
$game->printGameMap();
for ($i = 0; $i <= 300; $i++) {
    $direct = mt_rand(0, 3);
    $game->playerMove($redId, \App\Model\Player::DIRECTION[$direct]);
    if ($game->isGameOver()) {
        echo "game_over" . PHP_EOL;
        return;
    }
    $direct = mt_rand(0, 3);
    $game->playerMove($blueId, \App\Model\Player::DIRECTION[$direct]);
    if ($game->isGameOver()) {
        echo "game_over" . PHP_EOL;
        return;
    }
//打印移动后战局
    echo PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL . PHP_EOL;
    $game->printGameMap();
    usleep(200000);
}