<?php

require_once __DIR__ . "/../vendor/autoload.php";

header('Content-type: application/json');

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
//echo json_encode($specificTitleWithLike->toArray());

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
    ['$skip' => 2], // Skip two records (already from limited 5)
    ['$sort' => ['pageCount' => -1]]
]);

//echo json_encode($onlySpecificFields->toArray());

$booksPublishedInSpecificYear = $collection->aggregate([
    ['$match' => [
        "publishedDate" =>
            [
                '$gt' => '2010-01-01',
                '$lte' => '2010-12-31',
            ]
    ]],
    ['$project' =>
        [
            '_id' => 0,
            'title' => 1,
            'publishedDate' => 1,
        ]
    ],
    ['$sort' => ['publishedDate' => 1]]
]);
//echo json_encode($booksPublishedInSpecificYear->toArray());

$differentThanPublished = $collection->aggregate([
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

//echo json_encode($differentThanPublished->toArray());

$categories = $collection->distinct("categories");
//json_encode($categories);

$numberOfBooksAboutJava = $collection->aggregate([
    ['$match' => ['shortDescription' => ['$regex' => 'Java']]],
    ['$group' => ['_id' => null, 'count' => ['$sum' => 1]]],
]);
//echo json_encode($numberOfBooksAboutJava->toArray());

$numberOfBooksInStatuses = $collection->aggregate([
    ['$group' => ['_id' => '$status', 'count' => ['$sum' => 1]]],
    ['$sort' => ['count' => -1]],
]);

//echo json_encode($numberOfBooksInStatuses->toArray());

$booksByAuthor = $collection->aggregate([
    ['$unwind' => '$authors'],
//    ['$match' => ['authors' => 'Andrew Schmidt']],
    [
        '$group' =>
            [
                '_id' => [
                    '$title',
                    '$authors',
                ],
                'author' => ['$first' => '$authors'],
                'title' => ['$first' => '$title']
            ]
    ],
    [
        '$group' =>
            [
                '_id' => ['$author'],
                'author' => ['$first' => '$author'],
                'title' => ['$push' => '$title']
            ]
    ],

    ['$project' => [
        '_id' => 0,
        'result' => [
            'author' => '$author',
            'titles' => '$title',
        ],
    ]],
//    ['$out' => 'booksPerAuthor'] // - Put result into new collection
]);

$result = $client->playground->booksPerAuthor->find();
//echo json_encode($result->toArray());
//echo json_encode($booksByAuthor->toArray());

$numberOfBooksPerCategory = $collection->aggregate([
    ['$unwind' => '$categories'],
    ['$project' => [
        'categories' => ['$toLower' => '$categories']] // Project to treat as same eg. Java with java
    ],
    ['$group' => [
        '_id' => ['$categories'],
        'counter' => ['$sum' => 1]
    ]],
]);

echo json_encode($numberOfBooksPerCategory->toArray());