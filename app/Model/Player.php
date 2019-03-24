<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/3/16
 * Time: 19:28
 */

namespace App\Model;


class Player implements \JsonSerializable
{
    const UP = 'up';
    const DOWN = 'down';
    const LEFT = 'left';
    const RIGHT = 'right';
    const DIRECTION = [self::UP, self::DOWN, self::LEFT, self::RIGHT];

    const PLAYER_TYPE_SEEK = 1;
    const PLAYER_TYPE_HIDE = 2;

    private $id;
    private $type = self::PLAYER_TYPE_SEEK;
    private $x;
    private $y;

    public function __construct($id)
    {
        $this->id = $id;
    }

    public function setCoordinate($x, $y)
    {
        $this->x = $x;
        $this->y = $y;
    }

    public function up()
    {
        $this->x--;
    }

    public function down()
    {
        $this->x++;
    }

    public function left()
    {
        $this->y--;
    }

    public function right()
    {
        $this->y++;
    }

    /**
     * @return mixed
     */
    public function getX()
    {
        return $this->x;
    }

    /**
     * @return mixed
     */
    public function getY()
    {
        return $this->y;
    }

    /**
     * @param string $type
     */
    public function setType($type)
    {
        $this->type = $type;
    }

    public function jsonSerialize()
    {
        return [
            'id' => $this->id,
            'type' => $this->type,
            'x' => $this->x,
            'y' => $this->y,
        ];
    }

    /**
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }
}