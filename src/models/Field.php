<?php
    namespace Absolute\ChangeTrackerPhpSdk\Model;

    class Field {
        public string $name;
        public string $prevValue;
        public string $nextValue;

        /**
         * @constructor
         * @param string $name
         * @param string $prevValue
         * @param string $nextValue
         */
        function __construct(string $name, string $prevValue = '', string $nextValue = '') {
            $this->name = $name;
            $this->prevValue = $prevValue;
            $this->nextValue = $nextValue;
        }

        public function setName(string $name) : void{
            $this->name = $name;
        }

        public function getName() : string{
            return $this->name;
        }

        public function setPrevValue(string $prevValue) : void{
            $this->prevValue = $prevValue;
        }

        public function getPrevValue() : string {
            return $this->prevValue;
        }


        public function setNextValue(string $nextValue) : void{
            $this->nextValue = $nextValue;
        }

        public function getNextValue() : string {
            return $this->nextValue;
        }

        public function toString() : string {
            $name = $this->name;
            $prev = $this->prevValue; 
            $next = $this->nextValue;
            if (strtolower($prev) === strtolower($next))
                return $name . '=(' . $next . ')';

            // modified
            return $name . '=(' . (empty($prev) ? '' : $prev) . ' => ' . (empty($prev) ? '' : $next) . ')';
        }
    }
