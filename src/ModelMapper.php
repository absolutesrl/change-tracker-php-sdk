<?php

    namespace Absolute\ChangeTrackerPhpSdk;

    use Absolute\ChangeTrackerPhpSdk\Model\Field;
    use Absolute\ChangeTrackerPhpSdk\Model\Row;
    use Absolute\ChangeTrackerPhpSdk\Model\Table;
    use DateTime;
    use DateTimeInterface;
    use Exception;
    use stdClass;

    class ModelMapper
    {
        public object $model;
        public object $fields;

        function __construct(object $model)
        {
            $this->model = $model;
            $this->fields = new stdClass();
        }

        /**
         * Map all the primitive fields of the model (string, number, boolean, Date)
         * @returns ModelMapper ModelMapper instance
         **/
        public function mapAll(): ModelMapper
        {
            $keys = array_keys((array)$this->model);
            foreach ($keys as $key) {
                if (!$this->isSimpleType($this->model->$key)) continue;
                $this->fields->$key = fn() => $this->model->$key;
            }
            return $this;
        }

        /**
         * Map a single field using function or dot-separed string
         * @param callable|string $mapping - function or dot-separed string used to map a field of the model
         * @param string $fieldName - the name of mapped field in the result model
         * @return ModelMapper
         * @throws Exception
         */
        public function map(callable|string $mapping, string $fieldName): ModelMapper
        {
            if (!$mapping || !$fieldName) throw new Exception('ChangeTracker, ModelMapper, Map Error:invalid mapping' . $mapping . 'or field ' . $fieldName);

            if (is_callable($mapping)) {
                $this->fields->$fieldName = $mapping;
                return $this;
            }

            if (gettype($mapping) !== 'string') throw new Exception('ChangeTracker, ModelMapper, Map Error: mapper should be of type function or string');

            //non empty tokens only
            $tokens = array_filter(explode('.', $mapping), fn($el) => !!$el);

            $currentModel = $this->model;

            foreach ($tokens as $token) {
                $currentModel = gettype($currentModel) === 'object' && isset($currentModel->$token);

                //breaks validation control and maps error anyway
                //the error is managed by toList function;
                if (!$currentModel)
                    break;
            }

            //iterates through model to retrieve
            $this->fields->$fieldName = fn($m) => array_reduce($tokens, fn($p, $c) => $p->$c, $m);

            return $this;
        }

        /**
         * ignore - ignore a specific field
         * @param string $fieldName - the name of mapped field in the result model
         * @returns ModelMapper ModelMapper instance
         **/
        public function ignore(string $fieldName): ModelMapper
        {
            unset($this->fields->$fieldName);
            return $this;
        }

        /**
         * returns an array of mapped fields
         * @returns Field[] the mapped fields
         **/
        public function toList(): array
        {
            $keys = array_keys((array)$this->fields);

            $newFields = [];

            foreach ($keys as $key) {
                try {
                    if (!property_exists($this->fields, $key)) throw new Exception('ChangeTracker, Error generating Field model for field ' . $key);
                    $newField = new Field($key, $this->convertValue(call_user_func($this->fields->$key, $this->model)));
                    if (!empty($newField->prevValue)) $newFields[] = $newField;
                } catch (Exception $ex) {
                    echo $ex;
                }
            }

            return $newFields;
        }

        /**
         * returns
         * @param string $key - the row key value
         * @param Table[] $linkedTables - linked tables
         * @returns Row the mapped fields
         **/
        public function toRow(string $key, array $linkedTables = null): Row
        {
            $row = new Row($key);

            $row->fields = $this->toList();

            if (is_array($linkedTables))
                $row->tables = $linkedTables;

            return $row;
        }

        private function convertValue($value): string
        {
            switch (gettype($value)) {
                case "NULL":
                    return '';
                case 'object':
                    if ($value instanceof DateTime)
                        return $value->format(DateTimeInterface::ATOM);
                    break;
                case 'boolean':
                case "integer":
                case "double":
                case 'string':
                    return strval($value);
            }
            return '';
        }

        private function isSimpleType($value): bool
        {
            return match (gettype($value)) {
                'string', 'boolean', 'integer', 'double' => true,
                'object' => $value instanceof DateTime,
                default => false,
            };

        }
    }