<?php


spl_autoload_register(function ($class) {
    $fileName = str_replace("\\", DIRECTORY_SEPARATOR, "$class.php");
    if(file_exists(stream_resolve_include_path($fileName))) {
        include_once $fileName;
    } elseif(file_exists(stream_resolve_include_path('Libraries' . DIRECTORY_SEPARATOR . $fileName))) {
        include_once 'Libraries' . DIRECTORY_SEPARATOR . $fileName;
    }

    if (class_exists($class) and method_exists($class, 'init')) {
        $class::init();
    }
});
