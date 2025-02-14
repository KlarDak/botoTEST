<?php
    namespace KlarDak\FeedbackDB;
    
    use KlarDak\FeedbackDB\Types\FeedbackConnector;
    use KlarDak\FeedbackDB\Types\FeedbackMessage;

    class FeedbackDB {
        public $feedbackDBConnector;
        
        public function __construct(FeedbackConnector $DBConnector) {
            $this->feedbackDBConnector = $DBConnector;
        }

        public function addMessage(string $message_text, int $user_id, ?int $message_id = null, ?string $file_url = null): bool {
            $addQuery = "INSERT INTO messages (user_id, message_id, message_text, file_url, date_created) VALUES (:user_id, :message_id, :message_text, :file_url, NOW())";
            $addParams = [
                ":user_id" => $user_id, 
                ":message_id" => $message_id, 
                ":message_text" => $message_text, 
                ":file_url" => $file_url, 
            ];
            return $this->feedbackDBConnector->returnBoolPrepare($addQuery, $addParams);
        }

        public function addReplyToMessage(int $user_id, int $regist_message_id, string $message_text, ?string $file_url = null) : bool {
            $addReplyQuery = "INSERT INTO messages (user_id, message_text, file_url, date_created) VALUES (:user_id, :message_text, :file_url, NOW())";
            $addReplyParams = [
                ":user_id" => $user_id,
                ":message_text" => $message_text,
                ":file_url" => $file_url,
            ];
            

            if ($this->feedbackDBConnector->returnBoolPrepare($addReplyQuery, $addReplyParams)) {
                $checkLastID = $this->getLastInsertID();

                if ($checkLastID) {
                    return $this->feedbackDBConnector->returnBoolPrepare(
                        "UPDATE messages SET is_answer = 1, answer_message_id = :ami WHERE regist_message_id = :rmi",
                        [
                            ":ami" => $checkLastID,
                            ":rmi" => $regist_message_id
                        ]
                    );
                }
            }

            return false;
        }
        
        public function getMessage(int $regist_message_id) : FeedbackMessage {
            $getUserInfo = $this->feedbackDBConnector->returnArrayPrepare(
                "SELECT * FROM messages WHERE regist_message_id = :rmi AND is_dropped = 0",
                [
                    ":rmi" => $regist_message_id
                ]
            );

            return new FeedbackMessage($getUserInfo[0]);
        }
    
        /**
         * Function for check unread messages (NOT WORK NOW!!!!)
         * @param int $count_messages
         * @param int $page
         * @return array
         */
        public function getUnreadMessages(int $count_messages, int $page) : array {
            $unreadMessages =  $this->feedbackDBConnector->returnArrayQuery(
                "SELECT regist_message_id, user_id, message_id, message_text, file_url, date_created FROM messages WHERE is_answer = 0 AND is_dropped = 0 LIMIT {$count_messages} OFFSET " . $count_messages * ($page - 1),
            );

            $arrayWithMessages = [];

            foreach ($unreadMessages as $key => $value) {
                $arrayWithMessages[$key] = new FeedbackMessage($value);
            }

            return $arrayWithMessages;
        }
        public function getCountOfUnreadMessages() : int {
            $getCountMessages = $this->feedbackDBConnector->returnArrayQuery("SELECT COUNT(regist_message_id) FROM messages");

            return $getCountMessages[0]["COUNT(regist_message_id)"];
        }

        public function dropMessage(int $regist_message_id) : bool {
            $isDropped = $this->feedbackDBConnector->returnBoolPrepare(
                "UPDATE messages SET is_dropped = 1 WHERE regist_message_id = :rmi",
                [
                    ":rmi" => $regist_message_id
                ]
            );

            $isUpdated = $this->feedbackDBConnector->returnBoolPrepare(
                "UPDATE messages SET answer_message_id = null WHERE answer_message_id = :ami",
                [
                    ":ami" => $regist_message_id
                ]
            );

            return ($isDropped && $isUpdated) ? true : false;
        }
        
        public function getUser(int $user_id) : FeedbackBotUser {
            $feedbackBotUser = new FeedbackBotUser($this->feedbackDBConnector, $user_id);
            $feedbackBotUser->setUserInfo();

            return $feedbackBotUser;
        }

        public function getAdmins() : FeedbackBotAdmin {
            return new FeedbackBotAdmin($this->feedbackDBConnector);
        }

        private function getLastInsertID() : int {
            return $this->feedbackDBConnector->feedbackDBConnector->lastInsertId();
        }
    }