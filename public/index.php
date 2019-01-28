<?php

require_once __DIR__ . "/../vendor/autoload.php";

$collection = (new MongoDB\Client('mongodb://mongodb/'))->test->books;

$results = $collection->find([
    'title' => [
        '$regex' => '.*PHP.*'
    ]
]);

var_dump($results->toArray());