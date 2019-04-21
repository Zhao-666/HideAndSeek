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
        DataCenter::pushPlayerToWaitList($playerId);
    }
}