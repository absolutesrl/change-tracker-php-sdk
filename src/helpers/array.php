<?php
namespace Absolute\ChangeTrackerPhpSdk\Helper;
function any(array $array, callable $fn) : bool{
    foreach ($array as $value) {
        if(call_user_func($fn, $value)) {
            return true;
        }
    }
    return false;
}
function every(array $array, callable $fn) : bool{
    foreach ($array as $value) {
        if(!call_user_func($fn, $value)) {
            return false;
        }
    }
    return true;
}
function find(array $array, callable $fn) {
    foreach ($array as $value) {
        if($fn($value)) {
            return $value;
        }
    }
    return null;
}
function isDefaultValue($value) : bool{
    return $value === null || $value === '';
}
function isFull($model) : bool {
    return is_array($model->fields) && count($model->fields) > 0 || is_array($model->tables) && any($model->tables, fn($el) => (any($el->rows, 'isFull')));
}