<?php

namespace Absolute\ChangeTrackerPhpSdk\Core;

use Absolute\ChangeTrackerPhpSdk\Model\Table;
use Absolute\ChangeTrackerPhpSdk\Model\Row;
use Absolute\ChangeTrackerPhpSdk\Model\Field;
use Absolute\ChangeTrackerPhpSdk\Model\RowStatus;
use function Absolute\ChangeTrackerPhpSdk\Helper\find;
use function Absolute\ChangeTrackerPhpSdk\Helper\any;


/**
 * diff - produces a new Row which contains the differences between prev and next model
 * @param string $tableName
 * @param Row|null $prev
 * @param Row|null $next
 * @return Row|null
 */
class ChangeCalculator implements ChangeCalculatorInterface {

    public function diff(Row $prev = null, Row $next = null) {
        $diffModel = new Row();

        $prevIsSet = isset($prev);
        $nextIsSet = isset($next);

        if($prevIsSet) $diffModel->key = $prev->key;
        if (!$prevIsSet && $nextIsSet) $diffModel->key = $next->key;
        if (!$prevIsSet && !$nextIsSet) {
            echo "ChangeTracker, diff: missing or inSet diff models";
            return null;
        }

        if (!$nextIsSet) $diffModel->state = RowStatus::DELETED;
        if (!$prevIsSet) $diffModel->state = RowStatus::NEW;

        if ($prevIsSet && !empty($prev->fields))
            foreach ($prev->fields as $field) {
                //ignore default field values ('', null, undefined)
                if (isset($diffModel->state) && $diffModel->state === RowStatus::DELETED && $this->isDefaultValue($field->prevValue)) continue;

                $diffField = new Field($field->name, $field->prevValue);

                $diffModel->fields[] = $diffField;
            }

        if ($nextIsSet && !empty($next->fields)) {
            foreach ($next->fields as $field) {
                //ignore default field values ('', null, undefined)
                if (isset($diffModel->state) && $diffModel->state === RowStatus::NEW && $this->isDefaultValue($field->prevValue)) continue;

                $diffField = find($diffModel->fields ?? [], fn($el) => strtolower($field->name) === strtolower($el->name));

                if (!$diffField) {
                    $diffField = new Field($field->name, '', $field->prevValue);
                    $diffModel->fields[] = $diffField;
                } else
                    $diffField->nextValue = $field->prevValue;
            }
        }

        // Prende solo quelli differenti
        $diffModel->fields = array_filter($diffModel->fields ?? [], fn($el) => strtolower($el->prevValue) !== strtolower($el->nextValue));

        if (!isset($diffModel->state))
            $diffModel->state = count($diffModel->fields) > 0
                ? RowStatus::MODIFIED
                : RowStatus::UNCHANGED;

        if ($prev && isset($prev->tables) && count($prev->tables) > 0) {
            foreach ($prev->tables as $table) {
                $addedTable = new Table($table->name);
                $diffModel->tables[] = $addedTable;

                foreach ($table->rows as $row) {
                    if(isset($next)) $nextTable = find($next->tables, fn($el) => ($el->name) === ($table->name));

                    if(isset($nextTable)) $nextRow = find($nextTable->rows, fn($el) => $el->key === $row->key);

                    $diffRow = $this->diff($row, $nextRow ?? null);
                    if ($diffRow && $this->isFull($diffRow))
                        $addedTable->rows[] = $diffRow;
                }
            }
        }

        if ($next && isset($next->tables) && count($next->tables) > 0)
            foreach ($next->tables as $table) {
                $addedTable = find($diffModel->tables, fn($el) => $el->name === $table->name);
                if (!$addedTable) {
                    $addedTable = new Table($table->name);
                    $diffModel->tables[] = $addedTable;
                }

                foreach ($table->rows as $row) {
                    if(isset($prev)) $prevTable = find($prev->tables, fn($el) => ($el->name) === ($table->name));
                    if(isset($prevTable) && is_array($prevTable->rows)) $prevRow = find($prevTable->rows, fn($el) => $el->key === $row->key);

                    $diffRow = $this->diff($prevRow ?? null, $row);
                    $alreadyRow = find($addedTable->rows, fn($el) => $el->key === $row->key);

                    if (!$alreadyRow && $diffRow && $this->isFull($diffRow))
                        $addedTable->rows[] = $diffRow;
                }
            }

        $diffModel->tables = array_filter($diffModel->tables ?? [], fn($el) => is_array($el->rows) && count($el->rows) > 0);

        return $diffModel;
    }

    function isDefaultValue($value) : bool{
        return $value === null || $value === '';
    }
    function isFull($model) : bool {
        return is_array($model->fields) && count($model->fields) > 0 || is_array($model->tables) && any($model->tables, fn($el) => (any($el->rows, 'isFull')));
    }
}
