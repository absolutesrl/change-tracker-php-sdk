<?php
use Absolute\ChangeTrackerPhpSdk\Model\Row;
use Absolute\ChangeTrackerPhpSdk\Model\Table;
use Absolute\ChangeTrackerPhpSdk\Model\Field;

test('create field model', function(){
    $name = 'name';
    $prevValue = 'oldValue';
    $nextValue = 'newValue';
    $model = new Field($name, $prevValue, $nextValue);

    expect($model)->not->toBeNull();
    expect($model)->toMatchObject(['f'=>$name,'p' => $prevValue, 'n' => $nextValue]);

    $stringifiedModel = serialize($model);
    expect($stringifiedModel)->toContain($name);
    expect($stringifiedModel)->toContain($prevValue);
    expect($stringifiedModel)->toContain($name);
});

test('create row model and check if contains data', function(){
    $key = 'test key';
    $model = new Row($key);

    expect($model)->not->toBeNull();
    expect($model->key)->toBe($key);

    $model->fields[] = new Field('testField', 0, 1);

    expect($model->isFilled())->toBeTruthy();

    $subRow = new Row('test sub key');
    $subRow->fields[] = new Field('testSubField', 'a', 'b');
    $model->fields = [];
    $model->tables[] = new Table('testTable', [$subRow]);

    expect($model->isFilled())->toBeTruthy();
});

test('create_table model from constructor', function(){
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

});

test('create_table model from static method', function(){
    $tableName = 'tableName';
    $rowKey = 'testRowKey';
    $username = 'testUsername';
    $ipAddress = '127.0.0.1';

    /*
    //pass object instead of an array should return a null value
    $model = Table::createTable(new Row($rowKey), $tableName, $username, $ipAddress);

    expect($model)->toBeNull();
*/
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

});
