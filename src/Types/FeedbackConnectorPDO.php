<?php

    namespace KlarDak\FeedbackDB\Types;

    class FeedbackConnectorPDO implements FeedbackConnector {
        public \PDO $feedbackDBConnector;
        public function __construct(string $hostname, string $login, string $password, string $dbname, string $driver = "mysql") {
            $this->feedbackDBConnector = new \PDO("{$driver}:host={$hostname};dbname={$dbname}", $login, $password);
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

            return ($asResult !== false) ? $asResult->fetchAll($array_method ?? \PDO::FETCH_ASSOC) : [];
        }

        
        public function returnArrayPrepare(string $query, array $params, ?int $array_method = null) : array {
            $prepareQuery = $this->feedbackDBConnector->prepare($query);
            $asResult = $prepareQuery->execute($params);

            return ($asResult !== false) ? $prepareQuery->fetchAll($array_method ?? \PDO::FETCH_ASSOC) : [];
        }

        public function returnCount(string $query, ?array $params = []) : int {
            $query = $this->feedbackDBConnector->prepare($query);
            $query->execute($params);

            return (int) $query->fetchColumn();
        }

        public function getPDO() : \PDO {
            return $this->feedbackDBConnector;
        }
    }