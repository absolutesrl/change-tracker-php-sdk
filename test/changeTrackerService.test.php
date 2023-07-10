<?php
require_once(__DIR__.'/../src/models/Table.php');
require_once(__DIR__.'/../src/models/Row.php');
require_once(__DIR__.'/../src/changeTrackerService.php');

function changeTrackerServiceStoreDiffAndStoreData(){
    $generateToken = fn() => 'TESTTOKEN';
    $store = fn() => (['ok' => true]);
    $diff = fn() => (new Row('diffModel'));
    $createTable = fn() => (new Table());

    $changeTrackerService = makeChangeTrackerService(['generateToken' => $generateToken, 'store' => $store, 'diff'=> $diff, 'createTable'=> $createTable]);
    $service = $changeTrackerService('test', 'POSTSECRET', 10);

    $result = $service->store('ACCOUNTTEST', 'currentTestUser', 'test row', new Row('prevModel'), new Row('nextModel'), '127.0.0.1');

    var_dump($result);
};
changeTrackerServiceStoreDiffAndStoreData();
/*

test('changeTrackerService.store returns error in case of diff returns null', async () => {
$generateToken = () => 'TESTTOKEN';
$store = () => ({ok: true});
$diff = () => null;
$createTable = () => (new Table());

$changeTrackerService = makeChangeTrackerService({generateToken, store, diff, createTable})
$service = changeTrackerService('test', 'POSTSECRET');

$result = await service.store('ACCOUNTTEST', 'currentTestUser', 'test row', new Row('prevModel'), new Row('nextModel'), '127.0.0.1');

expect(result).toMatchObject({ok: false});
console.log(result.errorText)
})

test('changeTrackerService.store propagates remoteClient error', async () => {
$generateToken = () => 'TESTTOKEN';
$remoteClientResponse = {ok: false, errorText: 'TEST ERROR TEXT'};
$store = () => remoteClientResponse;
$diff = () => new Row('diffModel');
$createTable = () => (new Table());

$changeTrackerService = makeChangeTrackerService({generateToken, store, diff, createTable})
$service = changeTrackerService('test', 'GETSECRET','POSTSECRET');

$result = await service.store('ACCOUNTTEST', 'currentTestUser', 'test row', new Row('prevModel'), new Row('nextModel'), '127.0.0.1');

expect(result).toMatchObject(remoteClientResponse);
})

test('check changeTrackerService.getToken arguments', async () => {
$generateToken = (...args) => args.reduce((p, c) => p + '-' + c);

$defaultMockLambda = () => null;

$changeTrackerService = makeChangeTrackerService({
generateToken,
store: defaultMockLambda,
diff: defaultMockLambda,
createTable: defaultMockLambda
})
$service = changeTrackerService('test', 'GETSECRET', 'POSTSECRET');

$result = service.getToken('TABLENAME', 'ROWKEY');

$resultString ='GETSECRET-5-TABLENAME-ROWKEY';

expect(result).toBe(resultString);
})
*/