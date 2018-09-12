<?php
/**
 * Created by https://github.com/Wheiss
 * Date: 06.09.2018
 * Time: 23:56
 */

namespace App\Classes;


class Node
{
    /**
     * @var Position|null
     */
    public $position = null;
    // Это переменные, хранящие в себе препятствия
    // Если препятствия нет - переменная = null
    public $north = null;
    public $south = null;
    public $west = null;
    public $east = null;

    public function __construct(Position $position)
    {
        $this->position = $position;
    }

    public function getAvailableDirections()
    {
        $availableDirections = collect(Position::SIDES);
        $availableDirections = $availableDirections->mapWithKeys(function($item) {
            return [$item => $item];
        });
        // Если мы слева - налево идти нет смысла
        if($this->west) {
            $availableDirections->pull(Position::WEST);
        }
        // Если справа - направо идти нет смысла
        if($this->east) {
            $availableDirections->pull(Position::EAST);
        }
        // Если мы снизу - вниз идти нет смысла
        if($this->south) {
            $availableDirections->pull(Position::SOUTH);
        }
        // Если справа - направо идти нет смысла
        if($this->north) {
            $availableDirections->pull(Position::NORTH);
        }

        return $availableDirections;
    }

    public function addObstacle($side, Obstacle $obstacle)
    {
        $this->{$side} = $obstacle;
    }

    public function fillObstacles(Obstacle $obstacle)
    {
        foreach (Position::SIDES as $side) {
            $this->{$side} = $obstacle;
        }
    }

    public function destroyObstacle($side)
    {
        $this->{$side} = null;
    }

    public function eq($node)
    {
        $thisPos = $this->position;
        $nodePos = $node->position;
        return $thisPos->horizontal == $nodePos->horizontal && $thisPos->vertical == $nodePos->vertical;
    }
}