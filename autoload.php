<?php /** @noinspection PhpIncludeInspection */
/** @noinspection PhpIncludeInspection */


spl_autoload_register(function ($class) {
    $fileName = str_replace("\\", DIRECTORY_SEPARATOR, "$class.php");
    if(stream_resolve_include_path($fileName) !== false) {
        include_once $fileName;
    } elseif(stream_resolve_include_path('Libraries' . DIRECTORY_SEPARATOR . $fileName) !== false) {
        include_once 'Libraries' . DIRECTORY_SEPARATOR . $fileName;
    }

    if (class_exists($class) and method_exists($class, 'init')) {
        $class::init();
    }
});
