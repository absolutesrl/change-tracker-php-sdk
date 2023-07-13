<?php

require_once(__DIR__ . '/../vendor/autoload.php');
use Absolute\ChangeTrackerPhpSdk\ChangeTrackerService;
use Absolute\ChangeTrackerPhpSdk\ModelTracker;

// Initialize ChangeTracker
$ct = new ChangeTrackerService('YOUR_HOSTNAME', 'READ_SECRET', 'WRITE_SECRET', 5);

// Initial data
$data = json_decode('{"name": "John"}');

// Snapshot before changes
$prevMap = ModelTracker::mapAll($data)->toRow('id');

// Your data changes
$data->name = 'Jane';

// Snapshot after changes
$nextMap = ModelTracker::mapAll($data)->toRow('id');

// Store changes in ChangeTracker
$resp = $ct->store('table', 'you@company.com', 'Comment', $prevMap, $nextMap);


// Ignore attributes

$data = json_decode('{"last_edited_on": "2021-09-19T08:25:33.498Z", "price": 123}');

$prevMap = ModelTracker::mapAll($data)->ignore('last_edited_on')->toRow('id');

$data->price = 126;

$nextMap = ModelTracker::mapAll($data)->ignore('last_edited_on')->toRow('id');

$resp = $ct->store('table', 'you@company.com', 'Comment', $prevMap, $nextMap);


//Nested objects

$data = json_decode('{"price": 200, "customer": {"address": "4992 Harper Street"}}');

$prevMap = ModelTracker::mapAll($data)->map('customer.address', 'address')->toRow('id');

$data->customer->address = "2411 Coolidge Street";

$nextMap = ModelTracker::mapAll($data)->map('customer.address', 'address')->toRow('id');

$resp = $ct->store('table', 'you@company.com', 'Comment', $prevMap, $nextMap);


//Linked tables

$data = json_decode('{"id": "order_213", "price": 200, "address": "2411 Coolidge Street", "lines": [{"product_id": "prod_650", "quantity": 2}]}');

$toTable = fn($data) => ModelTracker::toTable('lines', array_map(fn($el) => ModelTracker::mapAll($el)->toRow($el->product_id), $data->lines));

$prevMap = ModelTracker::mapAll($data)->toRow($data->id, [$toTable($data)]);

$data->lines[] = json_decode('{"product_id": "prod_639", "quantity": 5}');

$nextMap = ModelTracker::mapAll($data)->toRow($data->id, [$toTable($data)]);

$resp = $ct->store('table', 'you@company.com', 'Comment', $prevMap, $nextMap);


/*
//Setting service hostname and keys
$service = $changeTracker('hostName', 'ApiGetKey', 'ApiPostKey', 5);
//store from service
$res = $ct->store('ACCOUNTTEST', 'currentTestUser', 'test row', new $Row('prevModel'), new $Row('nextModel'), '127.0.0.1');
//generate token
$token = $generateToken('apiKey', 'tableName', 'rowKey');
//store raw
$resp = $store('hostname', $token, $Table::createTable([new $Row('TESTROWKEY')], 'TESTTABLE', 'TESTUSER', '127.0.0.1'));
//new Classes
$table = new $Table();
$field = new $Field('fieldName');

var_dump($resp, $res, $token);

// Initial data
$data = new stdClass();
$data->price = 200;
$customer = new stdClass();
$customer->address = "4992 Harper Street";
$data->customer = $customer;
*/
