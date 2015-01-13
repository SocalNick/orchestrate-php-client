Orchestrate PHP Client
======================
A PHP client for [Orchestrate.io](http://orchestrate.io).

[![Build Status](https://travis-ci.org/SocalNick/orchestrate-php-client.png?branch=master)](https://travis-ci.org/SocalNick/orchestrate-php-client)
[![Latest Stable Version](https://poser.pugx.org/socalnick/orchestrate-php-client/v/stable.svg)](https://packagist.org/packages/socalnick/orchestrate-php-client) [![Total Downloads](https://poser.pugx.org/socalnick/orchestrate-php-client/downloads.svg)](https://packagist.org/packages/socalnick/orchestrate-php-client) [![Latest Unstable Version](https://poser.pugx.org/socalnick/orchestrate-php-client/v/unstable.svg)](https://packagist.org/packages/socalnick/orchestrate-php-client) [![License](https://poser.pugx.org/socalnick/orchestrate-php-client/license.svg)](https://packagist.org/packages/socalnick/orchestrate-php-client)

# Installation

```
$ composer require socalnick/orchestrate-php-client
```

# Creating a Client

```php
use SocalNick\Orchestrate\Client;
$client = new Client('your-api-key');
```

# Key / Value Operations

## Put (also used to create a collection)
```php
use SocalNick\Orchestrate\KvPutOperation;
$kvPutOp = new KvPutOperation("first_collection", "first_key", json_encode(array("name" => "Nick")));
$kvObject = $client->execute($kvPutOp);
$ref = $kvObject->getRef(); // 741357981fd7b5cb
```

## Put if-match
```php
use SocalNick\Orchestrate\KvPutOperation;
$kvPutOp = new KvPutOperation("first_collection", "second_key", json_encode(array("name" => "Terry")), array('if-match' => '741357981fd7b5cb'));
$kvObject = $client->execute($kvPutOp);
$ref = $kvObject->getRef(); // 0d1f15ab524a5c5a
```

## Put if-none-match
```php
use SocalNick\Orchestrate\KvPutOperation;
$kvPutOp = new KvPutOperation("first_collection", "second_key", json_encode(array("name" => "Bill")), array('if-none-match' => '*'));
$kvObject = $client->execute($kvPutOp); // null
```

## Post (auto-generated key)
```php
use SocalNick\Orchestrate\KvPostOperation;
$kvPostOp = new KvPostOperation("first_collection", json_encode(array("name" => "Nick")));
$kvObject = $client->execute($kvPostOp);
$ref = $kvObject->getRef(); // 741357981fd7b5cb
$key = $kvObject->getKey(); // 05fb279bc820dd05
```

## Patch (partial update - operations)
```php
use SocalNick\Orchestrate\KvPatchOperationsOperation;
$kvPatchOperationsOp = new KvPatchOperationsOperation('first_collection', 'third_key');
$kvPatchOperationsOp
  ->add('birth_place.city', 'New York')
  ->remove('birth_place.country')
  ->replace('birth_place.state', 'New York')
  ->copy('full_name', 'name')
  ->test('age', 28)
  ->inc('age', 1)
  ->inc('years_until_death', -1);
$result = $client->execute($kvPatchOperationsOp);
```

## Get
```php
use SocalNick\Orchestrate\KvFetchOperation;
$kvFetchOp = new KvFetchOperation("films", "the_godfather");
$kvObject = $client->execute($kvFetchOp);
$ref = $kvObject->getRef(); // 9c1bc18e60d93848
```

## Get a previous version by ref
```php
use SocalNick\Orchestrate\KvFetchOperation;
$kvFetchOp = new KvFetchOperation("first_collection", "second_key", "741357981fd7b5cb");
$kvObject = $client->execute($kvFetchOp);
$ref = $kvObject->getRef(); // 741357981fd7b5cb
```

## Delete
```php
use SocalNick\Orchestrate\KvDeleteOperation;
$kvDeleteOp = new KvDeleteOperation("first_collection", "first_key");
$result = $client->execute($kvDeleteOp); // true
```

## Delete with purge
```php
use SocalNick\Orchestrate\KvDeleteOperation;
$kvDeleteOp = new KvDeleteOperation("first_collection", "first_key", true);
$result = $client->execute($kvDeleteOp); // true
```

## List
```php
use SocalNick\Orchestrate\KvListOperation;
$kvListOp = new KvListOperation("films");
$kvListObject = $client->execute($kvListOp);
$count = $kvListObject->count(); // 10
$link = $kvListObject->getLink(); // /v0/films?limit=10&afterKey=the_godfather_part_2
```

## List with inclusive start key
```php
use SocalNick\Orchestrate\KvListOperation;
$kvListOp = new KvListOperation("films", 5, 'anchorman');
$kvListObject = $client->execute($kvListOp);
$count = $kvListObject->count(); // 5
$link = $kvListObject->getLink(); // /v0/films?limit=5&afterKey=pulp_fiction
```

## List with exclusive after key
```php
use SocalNick\Orchestrate\KvListOperation;
$kvListOp = new KvListOperation("films", 5, null, 'anchorman');
$kvListObject = $client->execute($kvListOp);
$count = $kvListObject->count(); // 5
$link = $kvListObject->getLink(); // /v0/films?limit=5&afterKey=shawshank_redemption
```

## Delete collection
```php
use SocalNick\Orchestrate\CollectionDeleteOperation;
$cDeleteOp = new CollectionDeleteOperation("first_collection");
$result = $client->execute($cDeleteOp); // true
```

# Search

## Default Search
```php
use SocalNick\Orchestrate\SearchOperation;
$searchOp = new SearchOperation("films");
$searchResult = $client->execute($searchOp);
$count = $searchResult->count(); // 10
$total = $searchResult->totalCount(); // 12
```

## Search with query, limit, offset, and sort
```php
use SocalNick\Orchestrate\SearchOperation;
$searchOp = new SearchOperation("films", "Genre:*Crime*", 2, 2, 'value.Title:asc');
$searchResult = $client->execute($searchOp);
$count = $searchResult->count(); // 2
$total = $searchResult->totalCount(); // 8
$firstKey = $searchResult->getValue()['results'][0]['path']['key']; // lock_stock_and_two_smoking_barrels
```

# Events

## Put defaults to now
```php
use SocalNick\Orchestrate\EventPutOperation;
$evPutOp = new EventPutOperation("films", "pulp_fiction", "comment", json_encode(array("message" => "This is my favorite movie!")));
$result = $client->execute($evPutOp); // true
```

## Put with timestamp
```php
use SocalNick\Orchestrate\EventPutOperation;
$evPutOp = new EventPutOperation("films", "pulp_fiction", "comment", json_encode(array("message" => "This is my favorite movie!")), 1395029140000);
$result = $client->execute($evPutOp); // true
```

## Get
```php
use SocalNick\Orchestrate\EventFetchOperation;
$evFetchOp = new EventFetchOperation("films", "pulp_fiction", "comment");
$evObject = $client->execute($evFetchOp);
$count = $evObject->count(); // 2
```

## Get with start and end
```php
use SocalNick\Orchestrate\EventFetchOperation;
$evFetchOp = new EventFetchOperation("films", "pulp_fiction", "comment", 1395029140000, 1395029140001);
$evObject = $client->execute($evFetchOp);
$count = $evObject->count(); // 1
```

# Graph

## Put
```php
use SocalNick\Orchestrate\GraphPutOperation;
$graphPutOp = new GraphPutOperation("films", "the_godfather", "sequel", "films", "the_godfather_part_2");
$result = $client->execute($graphPutOp); // true

$graphPutOp = new GraphPutOperation("films", "the_godfather_part_2", "sequel", "films", "the_godfather_part_3");
$result = $client->execute($graphPutOp); // true
```

## Get
```php
use SocalNick\Orchestrate\GraphFetchOperation;
$graphFetchOp = new GraphFetchOperation("films", "the_godfather", "sequel/sequel");
$graphObject = $client->execute($graphFetchOp);
$count = $graphObject->count(); // 1
```

## Get with limit and offset
```php
use SocalNick\Orchestrate\GraphFetchOperation;
$graphFetchOp = new GraphFetchOperation("directors", "francis_ford_coppola", "films_directed", 1, 1);
$graphObject = $client->execute($graphFetchOp);
$count = $graphObject->count(); // 1
$next = $graphObject->getNext(); // /v0/directors/francis_ford_coppola/relations/films_directed?limit=1&offset=2
$prev = $graphObject->getPrev(); // /v0/directors/francis_ford_coppola/relations/films_directed?limit=1&offset=0
```

## Delete
```php
use SocalNick\Orchestrate\GraphDeleteOperation;
$graphDeleteOp = new GraphDeleteOperation("films", "the_godfather", "sequel", "films", "the_godfather_part_2");
$result = $client->execute($graphDeleteOp); // true

$graphDeleteOp = new GraphDeleteOperation("films", "the_godfather_part_2", "sequel", "films", "the_godfather_part_3");
$result = $client->execute($graphDeleteOp); // true

use SocalNick\Orchestrate\GraphFetchOperation;
$graphFetchOp = new GraphFetchOperation("films", "the_godfather", "sequel");
$graphObject = $client->execute($graphFetchOp);
$count = $graphObject->count(); // 0

$graphFetchOp = new GraphFetchOperation("films", "the_godfather_part_2", "sequel");
$graphObject = $client->execute($graphFetchOp);
$count = $graphObject->count(); // 0
```
