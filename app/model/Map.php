<?php
/**
 * Created by PhpStorm.
 * User: Next
 * Date: 2019/3/16
 * Time: 19:28
 */

namespace App\Model;


class Map
{
    private $width;
    private $height;

    private $map = [
        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
        [0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0],
        [0, 1, 0, 1, 1, 1, 1, 1, 1, 1, 0, 0],
        [0, 0, 0, 0, 1, 0, 1, 1, 0, 1, 0, 0],
        [0, 1, 1, 0, 1, 1, 1, 1, 1, 1, 1, 0],
        [0, 1, 1, 1, 0, 1, 1, 1, 0, 1, 1, 0],
        [0, 1, 1, 1, 1, 1, 1, 1, 1, 0, 0, 0],
        [0, 1, 1, 0, 0, 1, 0, 1, 1, 0, 1, 0],
        [0, 1, 1, 0, 1, 1, 1, 1, 0, 1, 0, 0],
        [0, 1, 1, 0, 0, 1, 1, 0, 1, 1, 1, 0],
        [0, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 0],
        [0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0, 0],
    ];

    /**
     * Map constructor.
     * @param $width
     * @param $height
     */
    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
    }

    public function getMapData()
    {
        return $this->map;
    }

    public function printMap()
    {
        foreach ($this->map as $column) {
            foreach ($column as $item) {
                if (empty($item)) {
                    echo "墙，";
                }else{
                    echo "    ";
                }
            }
            echo PHP_EOL;
        }
    }
}