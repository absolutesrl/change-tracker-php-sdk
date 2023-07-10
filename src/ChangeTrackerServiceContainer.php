<?php

namespace Absolute\ChangeTrackerPhpSdk;

use Closure;

/**
 * makeChangeTrackerService - parent changeTrackerService class containing changeTracker functions
 * @type {class}
 * @param {array} $params
 * @param {function} $params['generateToken'] - utility to generate token
 * @param {function} $params['diff'] - diff between Row models
 * @param {function} $params['store'] - store diff data
 * @param {function} $params['createTable'] - create Table model to store
 **/
class ChangeTrackerServiceContainer
{
    public Closure $generateToken;
    public Closure $diff;
    public Closure $store;
    public Closure $createTable;

    /**
     * @constructor
     * @param array $params
     */
    function __construct(array $params)
    {
        ['generateToken' => $generateToken, 'store' => $store, 'diff' => $diff, 'createTable' => $createTable] = $params;
        $this->generateToken = $generateToken;
        $this->diff = $diff;
        $this->store = $store;
        $this->createTable = $createTable;
    }

    public function generateToken(...$params)
    {
        return call_user_func_array($this->generateToken, $params);
    }

    public function diff(...$params)
    {
        return call_user_func_array($this->diff, $params);
    }

    public function makeStore(...$params)
    {
        return call_user_func_array($this->store, $params);
    }

    public function createTable(...$params)
    {
        return call_user_func_array($this->createTable, $params);
    }
}