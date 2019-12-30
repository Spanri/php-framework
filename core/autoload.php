<?php

function autoloader($className) {
    $path = dirname(__DIR__).DIRECTORY_SEPARATOR.str_replace('\\', DIRECTORY_SEPARATOR, $className).'.php';
    if(file_exists($path)) {
        require_once $path;
    }
}

spl_autoload_register(null, false);
spl_autoload_extensions(".php");
spl_autoload_register('autoloader');
