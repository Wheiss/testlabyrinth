<?php
/**
 * Created by https://github.com/Wheiss
 * Date: 06.09.2018
 * Time: 23:26
 */

namespace App\Classes;


class LabyrinthGenerator
{
    private $width = null;
    private $height = null;
    private $currentNode = null;
    private $path = [];
    private $visitedNodes = [];
    private $nodes = [];
    private $pathCounter = 0;
    private $labyrinth = null;

    public function __construct($width, $height)
    {
        $this->width = $width;
        $this->height = $height;
        $this->path = collect();
        $this->visitedNodes = collect();
    }

    public function generate()
    {
        $width = $this->width;
        $height = $this->height;

        $labyrinth = &$this->labyrinth;
        $labyrinth = new Labyrinth();
        $labyrinth->width = $width;
        $labyrinth->height = $height;

        $entrySide = array_random(Position::SIDES);
        $exitSide = Position::getOppositeSide($entrySide);

        $entryPos = $this->getRandomSideNodePos($entrySide, $width, $height);
        $exitPos = $this->getRandomSideNodePos($exitSide, $width, $height);

        $entryNode = new Node($entryPos);
//        $exitNode = new Node($exitPos);

        $nodes = &$labyrinth->nodes;

        for ($horisontalIndex = 0; $horisontalIndex < $width; $horisontalIndex++) {
            for ($verticalIndex = 0; $verticalIndex < $height; $verticalIndex++) {
                $position = new Position($verticalIndex, $horisontalIndex);
                $node = new Node($position);
                $nodeIndex = $this->getNodeIndexByPosition($position);
                $nodes[$nodeIndex] = $node;

                if (!$this->addEntryPoint('entry', $node, $entryPos) && !$this->addEntryPoint('exit', $node, $exitPos)) {
                    foreach (Position::SIDES as $side) {
                        $node->addObstacle($side, new Wall);
                    }
                }
            }
        }

        $this->currentNode = $this->labyrinth->entry;

        $this->goRandom();

        return $labyrinth;
    }

    private function getNodeIndexByPosition($pos)
    {
        return $pos->getIndex();
    }

    /**
     * Добавляет входную/выходную точку в лабиринт если позиции сходятся
     * Врзвращает результат попытки добавления
     * @param $pointName
     * @param $node
     * @param $expectedPosition
     * @return bool
     */
    private function addEntryPoint($pointName, $node, $expectedPosition)
    {
        if ($node->position->horizontal == $expectedPosition->horizontal && $node->position->vertical == $expectedPosition->vertical) {
            $this->labyrinth->{$pointName} = $node;

            $node->fillObstacles(new Wall());
            $node = $this->addObstaclesToBorderNode($node, new Door());

            return true;
        }

        return false;
    }

    private function addObstaclesToBorderNode($node, Obstacle $obstacle)
    {
        $width = $this->width;
        $height = $this->height;
        $position = $node->position;

        if ($position->horizontal == 0) {
            $node->west = $obstacle;
        }
        if ($position->horizontal == $width - 1) {
            $node->east = $obstacle;
        }
        if ($position->vertical == 0) {
            $node->south = $obstacle;
        }
        if ($position->vertical == $height - 1) {
            $node->north = $obstacle;
        }

        return $node;
    }

    /**
     * Проверяет, находится ли позиция в границах лабиринта
     * @param $pos
     * @return bool
     */
    private function checkPosInBorders($pos)
    {
        return ($this->width > $pos->horizontal && $pos->horizontal >= 0) && ($this->height > $pos->vertical && $pos->vertical >= 0);
    }

    private function getAvailableDirections()
    {
        $node = $this->currentNode;
        $nodePos = $node->position;

        $availableDirections = collect();

        foreach (Position::SIDES as $side) {
            $newPos = clone $nodePos;
            $newPos->addDir($side);

            if(!$this->checkPosInBorders($newPos) || $this->isVisitedPos($newPos)) {
                continue;
            }

            $availableDirections->push($side);
        }

        return $availableDirections;
    }

    private function isVisitedPos($pos)
    {
        $visitedNodes = $this->visitedNodes;

        $posIndex = $this->getNodeIndexByPosition($pos);
        return $visitedNodes->has($posIndex);
    }

    private function goRandom()
    {
        $nodes = &$this->labyrinth->nodes;

        $availableDirections = $this->getAvailableDirections();
        $currentIndex = $this->getNodeIndexByPosition($this->currentNode->position);
        $this->visitedNodes->put($currentIndex, $this->currentNode);

        if ($availableDirections->isNotEmpty()) {
            $this->path->put($this->pathCounter, $this->currentNode);
            $this->pathCounter++;

            $direction = $availableDirections->random();

            $currentNode = &$this->currentNode;

            $currentNode->destroyObstacle($direction);

            $currentPos = $currentNode->position;
            $newPos = (clone $currentPos)->addDir($direction);
            $newIndex = $this->getNodeIndexByPosition($newPos);

            $nextNode = &$nodes[$newIndex];

            $oppositeDir = Position::getOppositeSide($direction);
            $nextNode->destroyObstacle($oppositeDir);
            $this->currentNode = $nextNode;

            $this->goRandom();
        } else {
            $this->goBack();
        }

        return;
    }

    private function goBack()
    {
        if ($this->path->isEmpty()) {
            return;
        }
        $this->currentNode = $this->path->pop();

        $this->goRandom();
    }

    private function getRandomSideNodePos($side, $width, $height)
    {
        if (in_array($side, array(Position::NORTH, Position::SOUTH))) {
            $horizontal = rand(0, $width - 1);
            $vertical = ($side == Position::NORTH ? $height - 1 : 0);
        } else {
            $vertical = rand(0, $width - 1);
            $horizontal = ($side == Position::EAST ? $width - 1 : 0);
        }

        return new Position($vertical, $horizontal);
    }
}