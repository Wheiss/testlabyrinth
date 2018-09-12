<?php

namespace App\Http\Controllers;

use App\Classes\LabyrinthGenerator;
use Illuminate\Http\Request;

class LabyrinthController extends Controller
{
    public function generate()
    {
        $width = 40;
        $height = 30;
        $generator = new LabyrinthGenerator($width, $height);

        $labyrinth = $generator->generate();

        $nodes = $labyrinth->nodes;
        $labyrinthString = '';

//        for($vIndex = $height - 1; $vIndex > 0; $vIndex--) {
//            $labyrinthString .= $this->drawLine($vIndex, $labyrinth);
//            $labyrinthString .= '<br>';
//        }
//        foreach ($labyrinth->nodes as $node) {
//            if($node->west) {
//                $labyrinthString .= '|';
//            }
//            if($node->south) {
//
//            }
//        }
        return view('labyrinth', ['labyrinth' => $labyrinth, 'width' => $width, 'height' => $height]);
    }

//    private function drawLine($vIndex, $labyrinth)
//    {
//        $line = $labyrinth->getLine($vIndex);
//        $lineStr = '';
//
//        foreach ($line as $node) {
//            $lineStr .= '&nbsp';
//            if($node->north) {
//                $lineStr .= '_';
//            } else {
//                $lineStr .= '&nbsp';
//            }
//            $lineStr .= '&nbsp';
//        }
//        $lineStr .= '<br>';
//
//        $prevNode = null;
//
//        foreach ($line as $node) {
//            if($node->west && (!empty($prevNode) && !$prevNode->east)) {
//                $lineStr .= '|';
//            } else {
////                $lineStr .= '.';
//            }
//            if($node->south) {
//                $lineStr .= '_';
//            } else {
//                $lineStr .= '&nbsp';
//            }
//            if($node->east) {
//                $lineStr .= '|';
//            } else {
//                $lineStr .= '&nbsp';
//            }
//            $prevNode = $node;
//        }
//        return $lineStr;
//    }
}
