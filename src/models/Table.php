<?php
    namespace Absolute\ChangeTrackerPhpSdk\Model;
    use DateTime;

    class Table {

        public string $name;
        public array $rows;
        public DateTime $odt;
        public string $ip;
        public string $user;

        /**
         * @constructor
         * @param string $name
         * @param array $rows
         */
        function __construct(string $name = '', array $rows = []) {
            $this->name = $name;
            $this->rows = $rows;
        }

        public function setOperationDateTime(DateTime $odt) : void {
            $this->odt = $odt;
        }

        public function getOperationDateTime() : DateTime {
            return $this->odt;
        }

        public static function createTable(array $rows, string $tableName, string $userName, string $ipAddress) {

            foreach ($rows as $row){
                if(!$row instanceof Row){
                    echo "<script>console.error('ChangeTracker, createTable: invalid rows model');</script>";
                    return null;
                }
            }

            $model = new Table($tableName);

            $model->user = $userName;
            $model->odt = new DateTime();
            $model->ip = $ipAddress;
            $model->rows = $rows;

            return $model;
        }
    }
