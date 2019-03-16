<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/3/16
 * Time: 19:28
 */

namespace app;


class Player
{
    const UP = 'up';
    const DOWN = 'down';
    const LEFT = 'left';
    const RIGHT = 'right';

    private $id;
    private $color;
    private $x;
    private $y;

    public function __construct($id, $color = "#f00")
    {
        $this->id = $id;
        $this->color = $color;
    }

    /**
     * @param string $color
     */
    public function setColor($color)
    {
        $this->color = $color;
    }

    public function up()
    {
        $this->y--;
    }

    public function down()
    {
        $this->y++;
    }

    public function left()
    {
        $this->x--;
    }

    public function right()
    {
        $this->x++;
    }
}