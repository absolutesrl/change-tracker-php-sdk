<?php
    class Prodotto{
        public string $idProdotto;
        public string $prodotto;
        public int $quantita;
        public float $importo;

        function __construct(string $idProdotto, string $prodotto, int $quantita, float $importo){
            $this->idProdotto = $idProdotto;
            $this->prodotto = $prodotto;
            $this->quantita = $quantita;
            $this->importo = $importo;
        }
    }
    class Utente{
        public string $descrizione;
        function __construct(string $descrizione){
            $this->descrizione = $descrizione;
        }
    }
    class Anagrafica{
        public string $descrizione;
        function __construct(string $descrizione){
            $this->descrizione = $descrizione;
        }
    }
    class MockModel {
        public string $id;
        public string $descrizione;

        public DateTime $data;
        public float $prezzo;
        public bool $flagBit;
        public string $testo;

        public Utente $utente;
        public Anagrafica $anagrafica;
        public array $righe;

        function __construct(string $id, string $descrizione, DateTime $data, float $prezzo, bool $flagBit, string $testo, Utente $utente, Anagrafica $anagrafica, array $prodotti)
        {
            $this->id = $id;
            $this->descrizione = $descrizione;
            $this->data = $data;
            $this->prezzo = $prezzo;
            $this->flagBit = $flagBit;
            $this->testo = $testo;
            $this->utente = $utente;
            $this->anagrafica= $anagrafica;
            $this->righe = $prodotti;
        }
    }