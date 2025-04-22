<?php

namespace App;

use Routes;

class Bootstrap
{
    public function __construct()
    {
        require "routes/web.php";
    }
}