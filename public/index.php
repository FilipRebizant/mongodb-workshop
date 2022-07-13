<?php

require_once __DIR__ . "/../vendor/autoload.php";

$collection = (new MongoDB\Client('mongodb://mongodb/'))->test->books;

$results = $collection->aggregate([

    ['$group' => ['_id' => '$status', 'count' => ['$sum' => 1]]],
    ['$sort' => ['count' => -1]],

]);

var_dump($results->toArray());