<?php

namespace Absolute\ChangeTrackerPhpSdk\Core;
use Absolute\ChangeTrackerPhpSdk\Model\Row;

interface ChangeCalculatorInterface {
    public function diff(Row $prev = null, Row $next = null);
}