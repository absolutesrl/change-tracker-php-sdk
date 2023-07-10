<?php
require_once(__DIR__.'/../src/core/token.php');

function generateJwtTokenWithTableNameOnly(){
    $secret = 'rAFnMkTCENg3Xp48G6H7Kky2fF7UUx';
    $tableName = 'testTable';

    $token = generateToken($secret, $tableName, '', 2);

    $decoded = base64_decode(explode('.', $token)[1]);
    var_dump(json_decode($decoded) );
};
//generateJwtTokenWithTableNameOnly();
function generateJwtTokenWithTableNameAndRowKey(){
    $secret = 'XDTpMcGHS8a2eGaiUZqHXLUmshSTWXBU';
    $tableName = 'testTable';
    $rowKey = 'testRowKey';

    $token = generateToken($secret, $tableName, $rowKey, 2);

    $decoded = base64_decode(explode('.', $token)[1]);
    var_dump(json_decode($decoded) );
};

//generateJwtTokenWithTableNameAndRowKey();

function generateJwtTokenWithTableNameAndRowKeyWithDefaultDuration(){
    $secret = 'XDTpMcGHS8a2eGaiUZqHXLUmshSTWXBU';
    $tableName = 'testTable';
    $rowKey = 'testRowKey';

    $token = generateToken($secret, $tableName, $rowKey, 2);

    $decoded = base64_decode(explode('.', $token)[1]);
    var_dump(json_decode($decoded) );
};

test('generate a jwt $token with tableName and $rowKey with default duration', function(){
    $secret = 'XDTpMcGHS8a2eGaiUZqHXLUmshSTWXBU';
    $tableName = 'testTable';
    $rowKey = 'testRowKey';

    $token = generateToken($secret, $tableName, $rowKey);

    $decoded = base64_decode($token);
    var_dump(json_decode($decoded) );
});

generateJwtTokenWithTableNameAndRowKeyWithDefaultDuration();