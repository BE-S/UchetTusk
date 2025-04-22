<?php

namespace App\Controller;

use Libs\Templater;

class Controller
{
    public function view(string $pathToTemplate, $variables) {
        Templater::view($pathToTemplate, $variables);
    }
}