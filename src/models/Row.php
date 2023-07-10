<?php
    namespace Absolute\ChangeTrackerPhpSdk\Model;
    use function Absolute\ChangeTrackerPhpSdk\Helper\any;

    class Row {
        public string $key;
        public string $state;
        public string $desc;
        public array $fields;
        public array $tables;

        function __construct(string $key = '') {
            $this->key = $key;
        }

        //isFilled - check if model contains data
        public function isFilled() : bool {

            return !empty($this->fields) && count($this->fields) > 0 ||
                    !empty($this->tables) && any($this->tables, function($el){
                        return is_array($el->rows) && any($el->rows, function($x) {
                            return method_exists($x, 'isFilled') && $x->isFilled();
                        });
                    });
                
        }
    }
