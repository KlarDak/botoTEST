<?php
    namespace KlarDak\FeedbackDB;

    use KlarDak\FeedbackDB\Types\FeedbackConnector;
    use KlarDak\FeedbackDB\Types\FeedbackUserInfo;

    class FeedbackBotBanUsers {
        private FeedbackConnector $DBConnector;

        public function __construct(FeedbackConnector $DBConnect) {
            $this->DBConnector = $DBConnect;
        }

        public function asCount() : int {
            $countOfUsers = $this->DBConnector->returnCount("SELECT COUNT(user_id) FROM usersbot WHERE is_banned = 1 AND is_dropped = 0", []);
            return $countOfUsers;
        }

        public function asList() : array {
            $getUsers = $this->DBConnector->returnArrayQuery("SELECT * FROM usersbot WHERE is_banned = 1 AND is_dropped = 0");
            $setUsersPrepare = [];
            $FeedbackUserInfo = new FeedbackUserInfo();

            foreach ($getUsers as $user) {
                $setUsersPrepare[] = $FeedbackUserInfo->makeAndReturn($user);
            }

            return $setUsersPrepare;
        }
    }