<?php
    require(__DIR__ . '/../src/ModelTracker.php');
    require_once(__DIR__. '/../src/helpers/array.php');
    require_once(__DIR__. '/MockModel.php');

function getMockModel() : MockModel{
    $utente = new Utente('Utente associato');
    $anagrafica = new Utente('Anagrafica associato');
    $prodotto1 = new Prodotto('P1', 'prodotto1', 10, 100.34);
    $prodotto2 = new Prodotto('P2', 'prodotto2', 20, 200.34);
    $model = new MockModel('PRIMA', 'Descrizione Prima', new DateTime(), 126.72,
        true, 'testo', array($utente), array($anagrafica), array($prodotto1, $prodotto2));

    return $model;
}

function testTrackingBase (){
    $model = getMockModel();

    $modelTracker = new ModelTracker();

    $map = $modelTracker->createMap($model)->mapAll()->ignore('testo')->map(fn($el) => $el->descrizione . " test", "descrizione");

    $fields = $map->toList();

    var_dump($fields);
}

//testTrackingBase();
function testRowTableModelWithLinkedTables(){
    $model = getMockModel();
    $modelTracker = new ModelTracker();

    $row = $modelTracker->createMap($model)->mapAll()->toRow('PRIMA', [
            $modelTracker->toTable('righe', array_map(fn($el) => $modelTracker->mapAll($el)->toRow($el->idProdotto), $model->righe))]
    );

    $linkedTable = $row->tables[0];
    $linkedRow = $row->tables[0]->rows[0];
};

function testMappingAssociazioni(){
    $model = getMockModel();
    $modelTracker = new ModelTracker();
    $map = $modelTracker->createMap($model)->map(fn($el) => $el->anagrafica->descrizione, 'anagrafica')->map('utente.descrizione', 'utente');

    $fields = $map->toList();

    var_dump($fields);
};
//testMappingAssociazioni();

function testMappingErroriMappingGestiti(){

    $model = getMockModel();
    $modelTracker = new ModelTracker();
    $map = $modelTracker->createMap($model)->map(fn($el) => $el->prodotto->descrizione, 'prodotto')->map('magazzino.descrizione', 'magazzino');

    $fields = $map->toList();
    var_dump($fields);
};

testMappingErroriMappingGestiti();