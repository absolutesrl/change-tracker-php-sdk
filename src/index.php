<?php
require_once(__DIR__ . '/../vendor/autoload.php');
use function Absolute\ChangeTrackerPhpSdk\makeChangeTrackerService;

$changeTrackerService = makeChangeTrackerService([
    'generateToken' => fn(...$args) => call_user_func_array('generateToken', $args),
    'diff' => fn(...$args) => call_user_func_array('diff', $args),
    'store' => fn(...$args) => call_user_func_array('store', $args),
    'createTable' => fn(...$args) => call_user_func_array('Table::createTable', $args)
]);

function changeTracker() : array {
    global $changeTrackerService;

    return [
            'changeTracker' => $changeTrackerService,
            'modelTracker' => 'modelTracker',
            'store' => 'store',
            'diff' => 'diff',
            'generateToken' => 'generateToken',
            'Field' => 'Field',
            'Row' => 'Row',
            'Table' => 'Table'
        ];
}

