<?php

require_once __DIR__ . "/../vendor/autoload.php";

$data = file_get_contents(__DIR__ . '/../data/books.json');
$books = json_decode($data, true);

$collection = (new MongoDB\Client('mongodb://mongodb/'))->test->books;
$insertManyResult = $collection->insertMany($books);

printf("Inserted %d document(s)\n", $insertManyResult->getInsertedCount());
