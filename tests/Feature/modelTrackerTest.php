<?php

require_once(__DIR__. '/MockModel.php');
use function Absolute\ChangeTrackerPhpSdk\Helper\any;
use function Absolute\ChangeTrackerPhpSdk\Helper\every;
use function Absolute\ChangeTrackerPhpSdk\Helper\find;
use Absolute\ChangeTrackerPhpSdk\ModelTracker;


function getMockModel() : MockModel{
    $utente = new Utente('Utente associato');
    $anagrafica = new Anagrafica('Anagrafica associato');
    $prodotto1 = new Prodotto('P1', 'prodotto1', 10, 100.34);
    $prodotto2 = new Prodotto('P2', 'prodotto2', 20, 200.34);
    $model = new MockModel('PRIMA', 'Descrizione Prima', new DateTime(), 126.72,
        true, 'testo', $utente, $anagrafica, array($prodotto1, $prodotto2));

    return $model;
}

test( 'test tracking base', function() {
    $model = getMockModel();

    $map = ModelTracker::createMap($model)->mapAll()->ignore('testo')->map(fn($el) => $el->descrizione . " test", "descrizione");

    $fields = $map->toList();

    expect($fields)->toBeArray();
    expect(count($fields))->toBe(5);
});

test('test row table model with linked tables', function (){
    $model = getMockModel();

    $row = ModelTracker::createMap($model)->mapAll()->toRow('PRIMA', [
        ModelTracker::toTable('righe', array_map(fn($el) => ModelTracker::mapAll($el)->toRow($el->idProdotto), $model->righe))]
    );

    expect($row->key)->toBe('PRIMA');
    expect(count($row->tables))->toBe(1);
    expect($row->tables[0]->name)->toBe('righe');

    $linkedTable = $row->tables[0];

    expect(count($linkedTable->rows) === 2 &&
        every($linkedTable->rows, fn($el) => $el->key === "P1" || $el->key === "P2"))->toBeTruthy();

    $linkedRow = $row->tables[0]->rows[0];

    expect(count($linkedRow->fields) === 4)->toBeTruthy();

});

test('test mapping associazioni', function (){
    $model = getMockModel();
    $map = ModelTracker::createMap($model)->map(fn($el) => $el->anagrafica->descrizione, 'anagrafica')->map('utente.descrizione', 'utente');

    $fields = $map->toList();

    expect($fields)->ToBeArray();
    expect(count($fields))->toBe(2);

    expect(any($fields, fn($el) => $el->name === 'anagrafica'))->toBeTruthy();
    expect(find($fields, fn($el) => $el->name === 'anagrafica')->prevValue)->toBe($model->anagrafica->descrizione);

    expect(any($fields, fn($el) => $el->name === 'utente'))->toBeTruthy();
    expect(find($fields, fn($el) => $el->name === 'utente')->prevValue)->toBe($model->utente->descrizione);
});


test('test mapping errori mapping gestiti su console', function(){
    //$logSpy = pest->spyOn(var_dump(), 'log');

    $model = getMockModel();
    $map = ModelTracker::createMap($model)->map(fn($el) => $el->prodotto->descrizione, 'prodotto')->map('magazzino.descrizione', 'magazzino');

    $fields = $map->toList();

    expect($fields)->toBeArray();
    expect(count($fields))->toBe(0);

    /*
    expect(logSpy).toHaveBeenCalledTimes(2);
    expect(logSpy.mock.calls[0]).toEqual(
        ['ChangeTracker, Error generating Field model for field "prodotto"']
    );
    expect(logSpy.mock.calls[1]).toEqual(
        ['ChangeTracker, Error generating Field model for field "magazzino"'],
    );*/
});

/*
$modelJson = '{
    "id": "PRIMA",
    "descrizione": "Descrizione Prima",
    "data": "'.gmdate("Y-m-d\TH:i:s\Z").'",
    "prezzo": 126.72,
    "flagBit": true,
    "testo": "testo",
    "utente": {"descrizione": "Utente associato"},
    "anagrafica": {"descrizione": "Anagrafica associata"},
    "righe": [
        {
            "idProdotto": "P1",
            "prodotto": "prodotto1",
            "qta": 10,
            "importo": 100.34
        },
        {
            "idProdotto": "P2",
            "prodotto": "prodotto2",
            "qta": 10,
            "importo": "100.34"
        }]
}';

 return json_decode($modelJson);
*/