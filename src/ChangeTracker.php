<?php
namespace Absolute\ChangeTrackerPhpSdk;

use Absolute\ChangeTrackerPhpSdk\Model\Field;
use Absolute\ChangeTrackerPhpSdk\Model\Row;
use Absolute\ChangeTrackerPhpSdk\Model\Table;
use function Absolute\ChangeTrackerPhpSdk\Core\generateToken;
use function Absolute\ChangeTrackerPhpSdk\Core\diff;
use function Absolute\ChangeTrackerPhpSdk\Core\store;
use Closure;

class ChangeTracker {

    public Closure $changeTracker;
    public ModelTracker $modelTracker;
    public Closure $store;
    public Closure $diff;
    public Closure $generateToken;
    public Row $row;
    public Field $field;
    public Table $table;

    function __construct(Closure $changeTracker, ModelTracker $modelTracker, Closure $store, Closure $diff, Closure $generateToken, Row $row, Field $field, Table $table)
    {
        $this->changeTracker = $changeTracker;
        $this->modelTracker = $modelTracker;
        $this->store = $store;
        $this->diff = $diff;
        $this->generateToken = $generateToken;
        $this->row = $row;
        $this->field = $field;
        $this->table = $table;
    }

    public static function service() : static {

        $changeTrackerService = makeChangeTrackerService([
            'generateToken' => fn(...$args) => generateToken(...$args),
            'diff' => fn(...$args) => diff(...$args),
            'store' => fn(...$args) => store(...$args),
            'createTable' => fn(...$args) => Table::createTable(...$args)
        ]);

        $store = fn(...$args) => store(...$args);
        $diff = fn(...$args) => diff(...$args);
        $generateToken = fn(...$args) => generateToken(...$args);

        return new ChangeTracker($changeTrackerService, new ModelTracker(), $store, $diff, $generateToken, new Row(), new Field(''), new Table());
    }
}

/*
return [
    'changeTracker' => $changeTrackerService,
    'modelTracker' => 'modelTracker',
    'store' => 'store',
    'diff' => 'diff',
    'generateToken' => 'generateToken',
    'Field' => 'Field',
    'Row' => 'Row',
    'Table' => 'Table'
];*/