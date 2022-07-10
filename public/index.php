<?php

require_once __DIR__ . "/../vendor/autoload.php";

$client = new MongoDB\Client('mongodb://mongodb/');
$collection = $client->playground->books;

// IMPORT DATA

//$sourceFile = '../data/books.json';
//$string = file_get_contents($sourceFile);
//$json = json_decode($string, true);
//$collection->insertMany($json);


$specificTitleWithLike = $collection->find([
    'title' => ['$regex' => 'Gnuplot in Act']
]);
//print_r($specificTitleWithLike->toArray());

$onlySpecificFields = $collection->aggregate([
    ['$project' =>
        [
            '_id' => 0,
            'title' => 1,
            "pageCount" => 1,
        ]
    ],
    ['$match' => ['pageCount' => ['$gt' => 800]]], // Get books with more than 800 pages
    ['$limit' => 5],
    ['$skip' => 2], // Skip first two records
    ['$sort' => ['pageCount' => -1]]
]);

//print_r($onlySpecificFields);


//    ['$group' => ['_id' => '$status', 'count' => ['$sum' => 1]]],
//    ['$sort' => ['count' => -1]],

$startDate = strtotime("2008-01-01 00:00:00");
$endDate = strtotime("2010-12-31 00:00:00");
$booksPublishedInSpecificYear = $collection->aggregate([
    ['$match' => [
        "publishedDate" =>
            [
                '$gt' => new MongoDB\BSON\UTCDateTime($startDate),
                '$lte' => new MongoDB\BSON\UTCDateTime($endDate)
            ]
    ]],
    ['$project' =>
        [
            '_id' => 0,
            'title' => 1,
            'publishedDate' => 1,
        ]
    ],
]);

//var_dump($booksPublishedInSpecificYear->toArray());


$res = $collection->aggregate([
  ['$project' => ['title' => 1, "publishedDate" => 1]],
  ['$match' =>  ['publishedDate' => new MongoDB\BSON\UTCDateTime(strtotime('2010-07-01'))]]
]);
print_r($res->toArray());

$notPublished = $collection->aggregate([
    ['$project' =>
        [
            '_id' => 0,
            'title' => 1,
            "pageCount" => 1,
            "status" => 1,
        ]
    ],
    ['$match' => ['status' => ['$ne' => 'PUBLISH']]],
]);

//var_dump($notPublished->toArray());

$categories = $collection->distinct("categories");
//var_dump($categories);
