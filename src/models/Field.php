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
