<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/4/20
 * Time: 13:52
 */

namespace App\Manager;

class Logic
{
    public function matchPlayer($playerId)
    {
        //将用户放入队列中
        DataCenter::pushPlayerToWaitList($playerId);
        //发起一个Task尝试匹配
    }
}