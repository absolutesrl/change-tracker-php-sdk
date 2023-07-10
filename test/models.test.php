<?php
require_once(__DIR__ . '/../src/ModelTracker.php');
require_once(__DIR__.'/../src/models/Row.php');
require_once(__DIR__.'/../src/models/Field.php');
require_once(__DIR__.'/../src/models/Table.php');

 function createFieldModel(){
    $name = 'name';
    $prevValue = 'oldValue';
    $nextValue = 'newValue';
    $model = new Field($name, $prevValue, $nextValue);

    expect($model)->not->toBeNull();
    expect($model)->toMatchObject(['name'=>$name,'prevValue' => $prevValue, 'nextValue' => $nextValue]);

    $stringifiedModel = serialize($model);
    expect($stringifiedModel)->toContain($name);
    expect($stringifiedModel)->toContain($prevValue);
    expect($stringifiedModel)->toContain($name);
};

function createRowModelCheckData(){
    $key = 'test key';
    $model = new Row($key);

    $model->fields[] = new Field('testField', 0, 1);


    $subRow = new Row('test sub key');
    $subRow->fields[] = new Field('testSubField', 'a', 'b');
    var_dump($model->isFilled());

    $model->fields = [];
    $model->tables[] = new Table('testTable', [$subRow]);
    var_dump($model->isFilled());
    var_dump($model);
};

//createRowModelCheckData();
function createTableModelFromConstructor(){
    $tableName = 'tableName';
    $model = new Table($tableName);

    expect($model)->not->toBeNull();
    expect($model->name)->toBe($tableName);
    expect($model->rows)->toBeArray();
    expect(count($model->rows))->toBe(0);

    $rowKey = 'testRowKey';
    $model = new Table($tableName, [new Row($rowKey)]);

    expect($model)->not->toBeNull();
    expect($model->name)->toBe($tableName);
    expect($model->rows)->toBeArray();
    expect(count($model->rows))->toBe(1);
    expect($model->rows[0])->toMatchObject(['key'=> $rowKey]);

};

//createTableModelFromConstructor();

function createTableModelFromStaticMethod(){
    $tableName = 'tableName';
    $rowKey = 'testRowKey';
    $username = 'testUsername';
    $ipAddress = '127.0.0.1';

    //pass object instead of an array should return a null value
    $model = Table::createTable([new Row($rowKey)], $tableName, $username, $ipAddress);

    expect($model)->toBeNull();

    //pass anything else should return null
    $model = Table::createTable([$rowKey], $tableName, $username, $ipAddress);

    expect($model)->toBeNull();

    $model = Table::createTable([new Row($rowKey)], $tableName, $username, $ipAddress);

    expect($model)->not->toBeNull();
    expect($model)->toMatchObject(['name'=> $tableName, 'user'=> $username, 'ip'=> $ipAddress]);
    expect($model->odt)->toBeInstanceOf(DateTime::class);
    expect($model->getOperationDateTime())->toBeInstanceOf(DateTime::class);
    expect($model->rows)->toBeArray();
    expect(count($model->rows))->toBe(1);
    expect($model->rows[0])->toMatchObject(['key'=> $rowKey]);

};
