<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/3/17
 * Time: 16:59
 */

namespace App\Manager;


class Logic
{
    public function matchPlayer($playerId)
    {
        DataCenter::pushPlayerToWaitList($playerId);
    }
}