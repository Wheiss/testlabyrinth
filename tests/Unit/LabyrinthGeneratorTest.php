<?php

namespace Tests\Unit;

use App\Classes\Door;
use App\Classes\Labyrinth;
use App\Classes\LabyrinthGenerator;
use App\Classes\Node;
use App\Classes\Obstacle;
use App\Classes\Position;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class LabyrinthGeneratorTest extends TestCase
{

    public function labyrinthProvider()
    {
        // Сперва сгененрируем лабиринт 5*5
        $width = 5;
        $height = 5;

        // Мы хотим генерировать лабиринт
        // Пусть ответом генератора будет структура данных Labyrinth
        $generator = new LabyrinthGenerator($width, $height);


        $labyrinth = $generator->generate();
        return [[$labyrinth, $width, $height]];
    }

    /**
     * A basic test example.
     *
     * @dataProvider labyrinthProvider
     * @return void
     */
    public function testGenerator($labyrinth, $width, $height)
    {

        $this->assertInstanceOf(Labyrinth::class, $labyrinth, ' Генератор лабиринта должен возвращать инстанс лабиринта');

        // Лабиринт содержит ячейки(ноды)
        $this->assertAttributeContainsOnly(Node::class, 'nodes', $labyrinth, null, 'Лабиринт должен содержать ноды');
        // Количество ячеек соответствует размеру лабиринта
        $nodesCount = $width * $height;
        $nodes = $labyrinth->nodes;
        $this->assertCount($nodesCount, $nodes, 'Количество нод лабиринта должно быть равно ' . $nodesCount);

        // Коллекция ожидаемых позиций координат
        $nodeExpectedPositions = collect();
        for($horisontalPos = 0; $horisontalPos < $width; $horisontalPos++) {
            for($verticalPos = 0; $verticalPos < $height; $verticalPos++) {
                $nodeExpectedPositions->put('h' . $horisontalPos . 'v' . $verticalPos, ['horisontal' => $horisontalPos, 'vertical' => $verticalPos]);
            }
        }

        // Пора задать координаты в нодах
        foreach ($nodes as $node) {
            $this->assertAttributeInstanceOf(Position::class, 'position', $node, 'Ячейка лабиринта должна содержать позицию');

            $position = $node->position;
            // Проверка, что позиция содержит координаты
            $this->assertObjectHasAttribute('horizontal', $position, 'Позиция ячейки должна содержать горизонтальную координату');
            $this->assertObjectHasAttribute('vertical', $position, 'Позиция ячейки должна содержать вертикальную координату');
            // Проверка корректности координат
            $nodeExpectedPositions->pull('h' . $position->horizontal . 'v' . $position->vertical);
        }

        // По прежнему проверка корректности координат
        $this->assertEmpty($nodeExpectedPositions, 'Сформированы некорректные ноды(позиции)');

    }

    /**
     * Проверка входа в лабиринт
     * @dataProvider labyrinthProvider
     * @param $labyrinth
     * @param $width
     * @param $height
     */
    public function testEntry($labyrinth, $width, $height)
    {
        // Теперь нужно проверить вход
        $entry = $labyrinth->entry;
        $this->assertInstanceOf(Node::class, $entry);

        $emptyWalls = $emptyWalls = $this->getNodeBorders($entry, $width, $height);



        // Проверим, что нода располагается на краю
        $this->assertNotEmpty($emptyWalls);

        foreach ($emptyWalls as $wall) {
            $this->assertAttributeInstanceOf(Door::class, $wall, $entry);
        }

    }

    private function getNodeBorders($node, $width, $height)
    {
        $borderWalls = [];
        switch ($node->position->horizontal) {
            case 0:
                $borderWalls[] = Position::WEST;
                break;
            case $width - 1:
                $borderWalls[] = Position::EAST;
                break;
        }

        switch ($node->position->vertical) {
            case 0:
                $borderWalls[] = Position::SOUTH;
                break;
            case $height - 1:
                $borderWalls[] = Position::NORTH;
                break;
        }

        return $borderWalls;
    }

    /**
     * Проверка выхода из лабиринта
     * @dataProvider labyrinthProvider
     * @param $labyrinth
     * @param $width
     * @param $height
     */
    public function testExit($labyrinth, $width, $height)
    {
        // Теперь нужно проверить выход
        $exit = $labyrinth->exit;
        $this->assertInstanceOf(Node::class, $exit);

        $emptyWalls = $this->getNodeBorders($exit, $width, $height);

        // Проверим, что нода располагается на краю
        $this->assertNotEmpty($emptyWalls);

        foreach ($emptyWalls as $wall) {
            $this->assertAttributeInstanceOf(Door::class, $wall, $exit);
        }

    }

    private $entry = null;
    /**
     *
     * @dataProvider labyrinthProvider
     * @param $labyrinth
     * @param $width
     * @param $height
     */
    public function testPath(Labyrinth $labyrinth, $width, $height)
    {
        $this->entry = $labyrinth->entry;

        $this->visitedNodes = collect([$this->entry]);
       // Попытаемся пройти лабиринт, а по дороге посмотрим на стены
        $this->goLabyrinth($labyrinth, $this->entry);

        // Проверим, что посетили все ячейки
    }

    private function getAvailableDirections($node, $labyrinth)
    {
        $availableDirections = collect();
        foreach ($node->getAvailableDirections() as $direction) {
            $nextNode = $labyrinth->getSibling($node, $direction);
            if(!$this->visitedNodes->has($nextNode->position->getIndex())) {
                $availableDirections->push($direction);
            }
        }

        return $availableDirections;
    }

    private $visitedNodes = [];
    private function goLabyrinth($labyrinth, $currentNode)
    {
        static $path = [];
        // Положим текущий нод в посещенные
        $this->visitedNodes->put($currentNode->position->getIndex(), $currentNode);

        // Получаем допустимые направления движения в рамках теста
        $availableDirections = $this->getAvailableDirections($currentNode, $labyrinth);

        // Если есть куда идти
        if($availableDirections->isNotEmpty()) {
            array_push($path, $currentNode);

            // Выберем случайное направление
            $direction = $availableDirections->random();

            // Получим нод по этому направлению
            $nextNode = $labyrinth->getSibling($currentNode, $direction);

            // Проверим, что это нода
            $this->assertInstanceOf(Node::class, $currentNode);

            // Проверим, что мы ее еще не посещали
            $this->assertNotContains($nextNode, $this->visitedNodes);

            // Если нода крайняя - надо проверить, что она имеет стенки по краям
            $nextPos = $nextNode->position;
            if($labyrinth->isOnBorder($nextNode)) {
                if($nextPos->vertical == 0) {
                    $this->assertAttributeInstanceOf(Obstacle::class, Position::SOUTH, $nextNode);
                }
                if($nextPos->vertical == $labyrinth->height - 1) {
                    $this->assertAttributeInstanceOf(Obstacle::class, Position::NORTH, $nextNode);
                }
                if($nextPos->horizontal == 0) {
                    $this->assertAttributeInstanceOf(Obstacle::class, Position::WEST, $nextNode);
                }
                if($nextPos->horizontal == $labyrinth->width - 1) {
                    $this->assertAttributeInstanceOf(Obstacle::class, Position::EAST, $nextNode);
                }
            }

            // Пройдем дальше
            $this->goLabyrinth($labyrinth, $nextNode);
        } else {
            if(empty($path)) {
                return;
            } else {
                $prevNode = array_pop($path);
                // Вернемся на предыдущий нод и попробуем пойти еще куда-нибудь
                $this->goLabyrinth($labyrinth, $prevNode);
            }
        }


        return;

    }
}
