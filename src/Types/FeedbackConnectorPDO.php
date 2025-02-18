<?php

    namespace KlarDak\FeedbackDB\Types;

    class FeedbackConnectorPDO implements FeedbackConnector {
        public \PDO $feedbackDBConnector;
        private array $DBSaver;
        public function __construct(string $hostname, string $login, string $password, string $dbname, string $driver = "mysql") {
            $this->DBSaver = [
                "hostname" => $hostname,
                "login" => $login,
                "password" => $password,
                "dbname" => $dbname,
                "driver" => $driver
            ];

            $this->connectToDB();
        }

        private function connectToDB() {
            $this->feedbackDBConnector = new \PDO("{$this->DBSaver['driver']}:host={$this->DBSaver['hostname']};dbname={$this->DBSaver['dbname']}", $this->DBSaver['login'], $this->DBSaver['password']);
        }

        public function returnBoolQuery($query) : bool {
            $asResult = $this->feedbackDBConnector->query($query);

            return ($asResult !== false) ? true : false;
        }

        public function returnBoolPrepare(string $query, array $params) : bool {
            $prepareQuery = $this->feedbackDBConnector->prepare($query);

            $asResult = $prepareQuery->execute($params);

            return ($asResult !== false) ? true : false;
        }

        public function returnArrayQuery(string $query, ?int $array_method = null) : array { 
            $asResult = $this->feedbackDBConnector->query($query);

            return ($asResult !== false) ? $asResult->fetchAll(\PDO::FETCH_ASSOC) : [];
        }

        
        public function returnArrayPrepare(string $query, array $params) : array {
            $prepareQuery = $this->feedbackDBConnector->prepare($query);

            $asResult = $prepareQuery->execute($params);

            return ($asResult !== false) ? $prepareQuery->fetchAll(\PDO::FETCH_ASSOC) : [];
        }

        public function returnCount(string $query, ?array $params = []) : int {
            $query = $this->feedbackDBConnector->prepare($query);
            $query->execute($params);

            return (int) $query->fetchColumn();
        }

        public function getPDO() : \PDO {
            return $this->feedbackDBConnector;
        }

        public function __sleep() {
            return ["DBSaver"];
        }

        public function __wakeup() {
            $this->connectTODB();
        }
    }