<?php
    namespace Absolute\ChangeTrackerPhpSdk;

    use Absolute\ChangeTrackerPhpSdk\Model\Table;
    use Absolute\ChangeTrackerPhpSdk\Model\Row;
    use Exception;

    class ModelTracker {
        /**
         * createMap - Create ModelMapper
         * @param object $model - the model to map
         * @returns ModelMapper ModelMapper instance
         **/
        public function createMap(object $model) : ModelMapper {
            return new ModelMapper($model);
        }

        /**
         * mapAll - Create ModelMapper and map alla the primitive fields of model (string, number, boolean, Date)
         * @param object $model - the model to map
         * @returns ModelMapper ModelMapper instance
         **/
        public function mapAll(object $model) : ModelMapper {
            $modelMapper = new ModelMapper($model);

            return $modelMapper->mapAll();
        }

        /**
         * map - Create ModelMapper and map a single field using function or dot-separed string
         * @param object $model - the model to map
         * @param string|callable $mapping
         * @param string $fieldName
         * @return ModelMapper
         * @throws Exception
         */
        public function map(object $model, string|callable $mapping, string $fieldName) : ModelMapper {
            $modelMapper = new ModelMapper($model);

            return $modelMapper->map($mapping, $fieldName);
        }

        /**
         * toTable - Utility to create a table model
         * @param string $name - the table name
         * @param Row[] $rows - array containing the table rows
         * @returns Table Table instance
         **/
        public function toTable (string $name, array $rows) : Table {
            return new Table($name, $rows);
        }
    }
