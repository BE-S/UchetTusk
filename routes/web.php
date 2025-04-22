<?php

namespace Routes;

use App\Controller\TableController;
use Libs\Http\Route;

$route = new Route();

$route->get("/{subtopicId}", TableController::class, "index");