<?php


spl_autoload_register(function ($class) {
    include_once str_replace("\\", "/", "$class.php"); //str_replace("\\", "/", substr("$class.php", 4));
    if (!(new ReflectionClass($class))->isAbstract() and method_exists($class, 'init')) {
        $class::init();
    }
});
