<?php

declare(strict_types=1);

namespace pLinq;

//autoloader for classes
spl_autoload_register(function($className) {
    $className = str_replace('\\', DIRECTORY_SEPARATOR, $className);
    $pieces = explode("/", $className);
    $file = __DIR__ . DIRECTORY_SEPARATOR . $pieces[1] . '.php';
    if (file_exists($file)) {
        require_once $file;

    }
});
// specified a global instance of the DAL
$dal = new DAL();