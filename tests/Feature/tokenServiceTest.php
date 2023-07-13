<?php

use Absolute\ChangeTrackerPhpSdk\Core\Token;


test('generate a jwt token with tableName only', function(){
    $tokenService = new Token();
    $secret = 'rAFnMkTCENg3Xp48G6H7Kky2fF7UUx';
    $tableName = 'testTable';

    $token = $tokenService->generateToken($secret, $tableName, '', 2);

    $decoded = json_decode(base64_decode(explode('.', $token)[1]));

    expect($decoded)->not->toBeNull();
    expect($decoded)->toMatchObject(['table' => $tableName]);
});

test('generate a jwt token with tableName and rowKey', function(){
    $tokenService = new Token();
    $secret = 'XDTpMcGHS8a2eGaiUZqHXLUmshSTWXBU';
    $tableName = 'testTable';
    $rowKey = 'testRowKey';

    $token = $tokenService->generateToken($secret, $tableName, $rowKey, 2);

    $decoded = json_decode(base64_decode(explode('.', $token)[1]));

    expect($decoded)->not->toBeNull();
    expect($decoded)->toMatchObject(['table'=> $tableName, 'key'=> $rowKey]);

});


test('generate a jwt $token with tableName and $rowKey with default duration', function(){
    $tokenService = new Token();
    $secret = 'XDTpMcGHS8a2eGaiUZqHXLUmshSTWXBU';
    $tableName = 'testTable';
    $rowKey = 'testRowKey';

    $token = $tokenService->generateToken($secret, $tableName, $rowKey);

    $decoded = json_decode(base64_decode(explode('.', $token)[1]));

    expect($decoded)->not->toBeNull();
    expect($decoded)->toMatchObject(['table'=> $tableName, 'key'=> $rowKey]);
    expect(gettype($decoded->iat))->toBe('integer');
    expect(gettype($decoded->exp))->toBe('integer');
    expect($decoded->exp - $decoded->iat)->toBe(300);
});
