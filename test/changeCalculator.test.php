<?php
require_once(__DIR__. '/../src/core/changeCalculator.php');
require_once(__DIR__ . '/../src/ModelTracker.php');
require_once(__DIR__. '/../src/helpers/array.php');
require_once(__DIR__ . '/../src/models/Field.php');
require_once(__DIR__ . '/../src/models/Row.php');
require_once(__DIR__ . '/../src/models/Table.php');
require_once(__DIR__ . '/../src/models/RowStatus.php');
require_once(__DIR__ . '/../tests/Feature/MockModel.php');
function getChangeCalculatorMockModel() : MockModel{
    $utente = new Utente('Utente associato');
    $anagrafica = new Anagrafica('Anagrafica associato');
    $prodotto0 = new Prodotto('P0', 'prodotto0', 1, 10);
    $prodotto1 = new Prodotto('P1', 'prodotto1', 10, 100.34);
    $prodotto2 = new Prodotto('P2', 'prodotto2', 20, 200.34);
    $model = new MockModel('PRIMA', 'Descrizione Prima', new DateTime(), 126.72,
        true, 'testo', $utente, $anagrafica, array($prodotto0, $prodotto1, $prodotto2));

    return $model;
}

function testDiffBase(){
    $model = getChangeCalculatorMockModel();
    $modelTracker = new ModelTracker();
    $prevMap = $modelTracker->mapAll($model)->toRow('PRIMA');

    $model->descrizione .= " modified";

    $interval = new DateInterval('P1D');
    $model->data->add($interval);

    $model->prezzo = 200.05;
    $model->flagBit = false;

    $nextMap = $modelTracker->mapAll($model)->toRow('PRIMA');

    $diff = diff('model', $prevMap, $nextMap);

    expect($diff)->toBeInstanceOf(Row::class);
    expect($diff->state)->toBe(RowStatus::MODIFIED);
    expect($diff->key)->toBe('PRIMA');
    expect($diff->fields)->toHaveLength(4);
    expect($diff->tables)->toHaveLength(0);
};

function testDiffAssociazioni() {
    $model = getChangeCalculatorMockModel();
    $modelTracker = new ModelTracker();

    $prevMap = $modelTracker->createMap($model)->toRow('PRIMA', [
        $modelTracker->toTable('righe', array_map(fn($el) => $modelTracker->mapAll($el)->toRow($el->idProdotto), $model->righe))]
);
    var_dump($model->righe);
    $model->righe[0]->importo = 20.0;
    var_dump($model->righe);
    array_splice($model->righe, 1, 1);
    $riga = new stdClass();
    $riga->idProdotto = 'P3';
    $riga->prodotto = 'prodotto3';
    $riga->quantita = 6;
    $riga->importo = 65.32;

    $model->righe[] = $riga;

    $nextMap = $modelTracker->createMap($model)->toRow('PRIMA', [
        $modelTracker->toTable('righe', array_map(fn($el) => $modelTracker->mapAll($el)->toRow($el->idProdotto), $model->righe))]
);

    $diff = diff('$model', $prevMap, $nextMap);

    expect($diff)->toBeInstanceOf(Row::class);
    expect($diff->key)->toBe('PRIMA');
    expect($diff->fields)->toHaveLength(0);
    expect($diff->tables)->toBeArray();
    expect($diff->tables)->toHaveLength(1);

    $table = $diff->tables[0];

    expect($table->name)->toBe('righe');
    expect($table->rows)->toBeArray();
    expect($table->rows)->toHaveLength(3); //3 rows modified: 1 updated, 1 added, 1 deleted
    expect(find($table->rows, fn($el) => $el->key === 'P0' && $el->state === RowStatus::MODIFIED))->toBeTruthy();
    expect(find($table->rows, fn($el) => $el->key === 'P1' && $el->state === RowStatus::DELETED))->toBeTruthy();
    expect(find($table->rows, fn($el) => $el->key === 'P3' && $el->state === RowStatus::NEW))->toBeTruthy();
};

testDiffAssociazioni();
/*

test('test $diff new', ()=>{
    const $model = getMockModel();
    const $nextMap = modelTracker.mapAll(model).toRow('PRIMA');

    const diff = changeCalculator.diff('model', null, nextMap);

    expect(diff).toBeInstanceOf(Row)
    expect(diff.state).toBe(RowStatus.New)
})

test('test diff delete', ()=>{
    const model = getMockModel();
    const $prevMap = modelTracker.mapAll(model).toRow('PRIMA');

    const diff = changeCalculator.diff('model', prevMap);

    expect(diff).toBeInstanceOf(Row)
    expect(diff.state).toBe(RowStatus.Deleted)
})

test('test diff unchanged', ()=>{
    const model = getMockModel();
    const prevMap = modelTracker.mapAll(model).toRow('PRIMA');

    //no model change

    const nextMap = modelTracker.mapAll(model).toRow('PRIMA');

    const diff = changeCalculator.diff('model', prevMap, nextMap);

    expect(diff).toBeInstanceOf(Row)
    expect(diff.state).toBe(RowStatus.Unchanged)
})
*/

/*
function getMockModel() {
    $model = new stdClass();
    $model->id = 'PRIMA';
    $model->descrizione = 'Descrizione Prima';
    $model->data = date('d/m/Y H:i:s');
    $model->prezzo = 126.72;
    $model->flagBit = true;
    $model->testo = 'testo';

    $utente = new stdClass();
    $utente->descrizione = 'Utente associato';
    $model->utente = $utente;

    $anagrafica = new stdClass();
    $anagrafica->descrizione = 'Anagrafica associata';
    $model->anagrafica = $anagrafica;

    $prodotto0 = new Prodotto('P0', 'prodotto0', 1, 10);
    $prodotto1 = new Prodotto('P1', 'prodotto1', 10, 100.34);
    $prodotto2 = new Prodotto('P2', 'prodotto2', 20, 200.34);

    $model->righe = array($prodotto0, $prodotto1, $prodotto2);


    return $model;
}*/

