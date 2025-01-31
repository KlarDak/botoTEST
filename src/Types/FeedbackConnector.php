<?php

    namespace KlarDak\FeedbackDB\Types;

    interface FeedbackConnector {
 
        /**
         * Use this method when you need just answer for query, without data in result
         * @param string $query
         * @return bool
         */
        public function returnBoolQuery(string $query) : bool;
        /**
         * Method for answer with just bool in result, without array's data
         * @param string $query
         * @param array $params
         * @return bool
         */
        public function returnBoolPrepare(string $query, array $params) : bool;
        /**
         * Method for answer with array in result (return empty array when data's not found)
         * @param string $query
         * @return array
         */
        public function returnArrayQuery(string $query) : array;
        /**
         * Method for answer with array in result with prepare for query (return empty array when data's not found)
         * @param string $query
         * @param array $params
         * @return array
         */
        public function returnArrayPrepare(string $query, array $params) : array;

        public function returnCount(string $query, ?array $params) : int;
    }