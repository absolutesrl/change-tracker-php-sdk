<?php
    namespace Absolute\ChangeTrackerPhpSdk\Model;

    use function PHPUnit\Framework\isEmpty;

    class Field {
        public string $f;
        public string $p;
        public string $n;

        /**
         * @constructor
         * @param string $name
         * @param string $prevValue
         * @param string $nextValue
         */
        function __construct(string $name, string $prevValue = '', string $nextValue = '') {
            $this->f = $name;
            $this->p = $prevValue;
            $this->n = $nextValue;
        }

        public function __set($name, $value)
        {
            if($name === 'name') $this->f = $value;
            if($name === 'prevValue') $this->p = $value;
            if($name === 'nextValue') $this->n = $value;
        }

        public function __get($name)
        {
            if($name === 'name') return $this->f;
            if($name === 'prevValue') return $this->p;
            if($name === 'nextValue') return $this->n;
            return $this->$name;
        }

        public function __isset($name)
        {
            if($name === 'name') return $this->f;
            if($name === 'prevValue') return $this->p;
            if($name === 'nextValue') return $this->n;
            return isset($this->$name);
        }

        public function toString() : string {
            $name = $this->f;
            $prev = $this->p;
            $next = $this->n;
            if (strtolower($prev) === strtolower($next))
                return $name . '=(' . $next . ')';

            // modified
            return $name . '=(' . (empty($prev) ? '' : $prev) . ' => ' . (empty($prev) ? '' : $next) . ')';
        }
    }
