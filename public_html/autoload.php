<?php


spl_autoload_register(function ($class) {
    $fileName = str_replace("\\", "/", "$class.php");
    if(file_exists(stream_resolve_include_path($fileName))) {
        include_once $fileName;
    }
    if (class_exists($class) and method_exists($class, 'init')) {
        $class::init();
    }
});
