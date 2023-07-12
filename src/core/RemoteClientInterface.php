<?php

namespace Absolute\ChangeTrackerPhpSdk\Core;
use Absolute\ChangeTrackerPhpSdk\Model\Table;

interface RemoteClientInterface
{
    public function store(string $hostName, string $token, Table $table = null);
}