<?php

use Illuminate\Support\Facades\Route;

foreach (glob(__DIR__.'/modules/*.php') as $file) {
    require $file;
}