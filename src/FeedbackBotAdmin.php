<?php
    namespace KlarDak\FeedbackDB;

    use KlarDak\FeedbackDB\Types\FeedbackConnector;
    use KlarDak\FeedbackDB\Types\FeedbackUserInfo;

    class FeedbackBotAdmin extends FeedbackUserInfo {
        protected FeedbackConnector $DBConnector;

        public function __construct(FeedbackConnector $DBConnector) {
            $this->DBConnector = $DBConnector;
        }
        public function asListID() : array {
            $getAdmins = $this->DBConnector->returnArrayQuery("SELECT user_id FROM usersbot WHERE is_admin = 1");

            return array_column($getAdmins, "user_id");
        }

        public function asList() : array {
            $getAdminsID = $this->DBConnector->returnArrayQuery("SELECT * FROM usersbot WHERE is_admin = 1");
            
            $adminQuery = [];
            foreach ($getAdminsID as $onceAdmin) {
                $adminQuery[] = $this->makeAndReturn($onceAdmin);
            }
            
            return $adminQuery;
        }

        public function asCount() : int {
            $getCountUsers = $this->feedbackDBConnector->returnArrayQuery("SELECT COUNT(is_admin) FROM usersbot");

            return $getCountUsers[0]["COUNT(regist_message_id)"];

        }
    }