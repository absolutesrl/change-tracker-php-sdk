<?php
use Absolute\ChangeTrackerPhpSdk\Core\ChangeCalculator;
use Absolute\ChangeTrackerPhpSdk\ModelTracker;
use Absolute\ChangeTrackerPhpSdk\Model\Row;
use Absolute\ChangeTrackerPhpSdk\Model\RowStatus;
use function Absolute\ChangeTrackerPhpSdk\Helper\find;

require_once(__DIR__ . '/MockModel.php');
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

test('test diff base', function(){
    $model = getChangeCalculatorMockModel();
    $changeCalculator = new ChangeCalculator();

    $prevMap = ModelTracker::mapAll($model)->toRow('PRIMA');

    $model->descrizione .= " modified";

    $interval = new DateInterval('P1D');
    $model->data->add($interval);

    $model->prezzo = 200.05;
    $model->flagBit = false;

    $nextMap = ModelTracker::mapAll($model)->toRow('PRIMA');

    $diff = $changeCalculator->diff($prevMap, $nextMap);

    expect($diff)->toBeInstanceOf(Row::class);
    expect($diff->state)->toBeString(RowStatus::MODIFIED);
    expect($diff->key)->toBeString('PRIMA');
    expect($diff->fields)->toHaveLength(4);
    expect($diff->tables)->toHaveLength(0);
});

test('test diff associazioni', function() {
    $model = getChangeCalculatorMockModel();
    $changeCalculator = new ChangeCalculator();

    $prevMap = ModelTracker::createMap($model)->toRow('PRIMA', [
        ModelTracker::toTable('righe', array_map(fn($el) => ModelTracker::mapAll($el)->toRow($el->idProdotto), $model->righe))]
    );
    $model->righe[0]->importo = 20.0;
    array_splice($model->righe, 1, 1);
    $riga = new stdClass();
    $riga->idProdotto = 'P3';
    $riga->prodotto = 'prodotto3';
    $riga->quantita = 6;
    $riga->importo = 65.32;

    $model->righe[] = $riga;

    $nextMap = ModelTracker::createMap($model)->toRow('PRIMA', [
        ModelTracker::toTable('righe', array_map(fn($el) => ModelTracker::mapAll($el)->toRow($el->idProdotto), $model->righe))]
    );

    $diff = $changeCalculator->diff($prevMap, $nextMap);

    expect($diff)->toBeInstanceOf(Row::class);
    expect($diff->key)->toBeString('PRIMA');
    expect($diff->fields)->toHaveLength(0);
    expect($diff->tables)->toBeArray();
    expect($diff->tables)->toHaveLength(1);

    $table = $diff->tables[0];

    expect($table->name)->toBeString('righe');
    expect($table->rows)->toBeArray();
    expect($table->rows)->toHaveLength(3); //3 rows modified: 1 updated, 1 added, 1 deleted
    expect(find($table->rows, fn($el) => $el->key === 'P0' && $el->state === RowStatus::MODIFIED))->toBeTruthy();
    expect(find($table->rows, fn($el) => $el->key === 'P1' && $el->state === RowStatus::DELETED))->toBeTruthy();
    expect(find($table->rows, fn($el) => $el->key === 'P3' && $el->state === RowStatus::NEW))->toBeTruthy();
});


test('test diff new', function(){
    $model = getChangeCalculatorMockModel();
    $changeCalculator = new ChangeCalculator();

    $nextMap = ModelTracker::mapAll($model)->toRow('PRIMA');

    $diff = $changeCalculator->diff(null, $nextMap);

    expect($diff)->toBeInstanceOf(Row::class);
    expect($diff->state)->toBeString(RowStatus::NEW);
});


test('test diff delete', function(){
    $model = getChangeCalculatorMockModel();
    $changeCalculator = new ChangeCalculator();

    $prevMap = ModelTracker::mapAll($model)->toRow('PRIMA');

    $diff = $changeCalculator->diff($prevMap);

    expect($diff)->toBeInstanceOf(Row::class);
    expect($diff->state)->toBeString(RowStatus::DELETED);
});

test('test $diff unchanged', function(){
    $model = getChangeCalculatorMockModel();
    $changeCalculator = new ChangeCalculator();

    $prevMap = ModelTracker::mapAll($model)->toRow('PRIMA');

    //no model change

    $nextMap = ModelTracker::mapAll($model)->toRow('PRIMA');

    $diff = $changeCalculator->diff($prevMap, $nextMap);

    expect($diff)->toBeInstanceOf(Row::class);
    expect($diff->state)->toBeString(RowStatus::UNCHANGED);
});

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

