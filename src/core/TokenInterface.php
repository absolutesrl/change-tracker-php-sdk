<?php

namespace Absolute\ChangeTrackerPhpSdk\Core;
interface TokenInterface
{
    public function generateToken(string $secret, string $tableName, string $rowKey, int $duration = 5);
}