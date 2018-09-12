<?php
/**
 * Created by https://github.com/Wheiss
 * Date: 06.09.2018
 * Time: 23:21
 */

namespace App\Classes;


class Labyrinth
{
    public $nodes = [];
    public $entry = null;
    public $exit = null;
    public $perimeterNodes = [];
    public $width = null;
    public $height = null;

    /**
     * Получает ячейку по индексу
     * @param $index
     * @return mixed
     */
    public function getNode($index)
    {
        return $this->nodes[$index];
    }

    public function getSibling($node, $direction)
    {
        $siblingPos = clone $node->position;
        $siblingPos->addDir($direction);
        return $this->getNode($siblingPos->getIndex());
    }

    public function getLine($vIndex)
    {
        return array_filter($this->nodes, function($item) use ($vIndex) {
            return $item->position->vertical == $vIndex;
        });
    }

    public function isOnBorder($node)
    {
        $nodePos = $node->position;
        return $nodePos->horizontal == 0 || $nodePos->horizontal == $this->width - 1 || $nodePos->vertical == 0 || $nodePos->vertical == $this->height - 1;
    }
}