<?php
/**
 * Created by https://github.com/Wheiss
 * Date: 07.09.2018
 * Time: 0:19
 */

namespace App\Classes;


class Position
{
    const NORTH = 'north';
    const SOUTH = 'south';
    const WEST = 'west';
    const EAST = 'east';
    const SIDES = [
        self::NORTH,
        self::SOUTH,
        self::WEST,
        self::EAST,
    ];
    public $vertical = null;
    public $horizontal = null;

    public function __construct($vertical, $horizontal)
    {
        $this->horizontal = $horizontal;
        $this->vertical = $vertical;
    }


    public static function getOppositeSide($side)
    {
        $oppositeSides = [
            self::NORTH => self::SOUTH,
            self::SOUTH => self::NORTH,
            self::WEST => self::EAST,
            self::EAST => self::WEST,
        ];

        if(!in_array($side, $oppositeSides)) {
            throw new \Exception('Incorrect side');

        }

        return $oppositeSides[$side];
    }

    public function addDir($direction)
    {
        switch ($direction) {
            case self::NORTH:
                $this->vertical++;
                break;
            case self::SOUTH:
                $this->vertical--;
                break;
            case self::EAST:
                $this->horizontal++;
                break;
            case self::WEST:
                $this->horizontal--;
                break;
        }

        return $this;
    }

    public function getIndex()
    {
        return 'h' . $this->horizontal . 'v' . $this->vertical;
    }

    public function eq(Position $position)
    {
        return $this->horizontal == $position->horizontal && $this->vertical == $position->vertical;
    }
}