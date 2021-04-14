<?php


ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
//error_reporting(E_ALL & ~E_NOTICE);
header('Content-Type:text/plain');

require 'autoload.php';

//$user = new Entities\User();
//
//$user->firstName = 'Kamyar';
//$user->lastName = 'Mirzavaziri';
//$user->store();

$type = new \Supers\Basics\Highers\Sequence([]);

$input = ['0' => 'a', '1' => 'b'];
var_dump(array_keys($input));
//$input = json_encode($input);
var_dump($type->accepts($input, $message), $message);
var_dump($input);
var_dump($type->toString());
var_dump($type->databaseType());

//$type = new Supers\Integers\NaturalUpperLimit(['end' => 30]);
//
//var_dump($type->toString());
//
//$type = new Supers\Integers\EfficientRange(['start' => 10, 'end' => 30]);
//
//var_dump($type->toString());
//
//$value = 25;
//var_dump($type->explicitlyAccepts($value, $message)); # dumps true
//$value = 5;
//var_dump($type->implicitlyAccepts($value)); # dumps true
//
//$value = 25;
//var_dump($type->implicitlyAccepts($value)); # dumps true
//
//$value = 15;
//var_dump($type->accepts($value)); # dumps true
//var_dump($value); # dumps 15
//
//$value = 5;
//var_dump($type->accepts($value)); # dumps true
//var_dump($value); # dumps 15
//
//$value = 5;
//var_dump($type->explicitlyAccepts($value, $reason)); # dumps false
//var_dump($reason); # dumps '5 is less than 10'
//
//$value = 30;
//var_dump($type->explicitlyAccepts($value, $reason)); # dumps false
//var_dump($reason); # dumps '30 is not less than 30'
//
//$value = 30;
//var_dump($type->accepts($value, $reasons)); # dumps false
//var_dump($reasons);
//
//$value = 5;
//var_dump($type->accepts($value, $reasons)); # dumps false
//var_dump($reasons);
//
//var_dump($type->DatabaseType()); # Dumps 'INT(6)'
