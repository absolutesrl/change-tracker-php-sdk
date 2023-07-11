<?php
namespace Absolute\ChangeTrackerPhpSdk;

use Absolute\ChangeTrackerPhpSdk\Model\Field;
use Absolute\ChangeTrackerPhpSdk\Model\Row;
use Absolute\ChangeTrackerPhpSdk\Model\Table;
use function Absolute\ChangeTrackerPhpSdk\Core\generateToken;
use function Absolute\ChangeTrackerPhpSdk\Core\diff;
use function Absolute\ChangeTrackerPhpSdk\Core\store;

class ChangeTracker {
    public ModelTracker $modelTracker;

    public Row $row;
    public Field $field;
    public Table $table;

    function __construct()
    {
        $this->modelTracker = new ModelTracker();
        $this->row = new Row();
        $this->field = new Field('');
        $this->table = new Table();
    }

    public function changeTracker(...$args) {

        $service = makeChangeTrackerService([
            'generateToken' => fn(...$args) => generateToken(...$args),
            'diff' => fn(...$args) => diff(...$args),
            'store' => fn(...$args) => store(...$args),
            'createTable' => fn(...$args) => Table::createTable(...$args)
        ]);

        return $service(...$args);
    }

    public function store(...$args): ?object
    {
        return store(...$args);
    }

    public function diff(...$args): ?Row
    {
        return diff(...$args);
    }
    public function generateToken(...$args): string
    {
        return generateToken(...$args);
    }
}
