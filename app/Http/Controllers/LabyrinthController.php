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

        return view('labyrinth', ['labyrinth' => $labyrinth, 'width' => $width, 'height' => $height]);
    }
}
