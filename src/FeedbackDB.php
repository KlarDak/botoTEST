<?php
    namespace KlarDak\FeedbackDB;

    use KlarDak\FeedbackDB\FeedbackUserData as FBDB;
    use KlarDak\FeedbackDB\FeedbackMessage as FBM;

    class FeedbackDB {
        protected $db_connector;

        function __construct(string $host, string $login, string $password, string $dbname, string $driver = "mysql") {
            $this->db_connector = new \PDO("{$driver}:host={$host};dbname={$dbname}", $login, $password);
        }

        public function getMessagesList(): array {
            $messageList = $this->db_connector->query("SELECT * FROM messages WHERE is_dropped = 0");
            return $messageList->fetchAll(\PDO::FETCH_ASSOC);
        }

        public function getMessage(int $regist_message_id): FBM|bool {
            if ($regist_message_id < 0) {
                return false;
            }

            $messageData = $this->db_connector->prepare("SELECT * FROM messages WHERE regist_message_id = :regist_id AND is_dropped = 0");
            $asResult = $messageData->execute([":regist_id" => $regist_message_id]);
            $messageDataResult = $messageData->fetch(\PDO::FETCH_ASSOC);

            return (count($messageDataResult) > 0) ? new FBM($messageDataResult) : false; 
        }

        public function getCountOfMessages(): int {
            $countOfMessages = $this->db_connector->query("SELECT COUNT(regist_message_id) FROM messages WHERE is_dropped = 0");
            $countResult = $countOfMessages->fetch();
            return $countResult[0];
        }
        
        public function addNewMessage(int $user_id, string $message_text, string $file_url = null, int $answer_to_message = null): bool {
            if ($user_id < 0 || ($user_id < 0 && $answer_to_message != null && $answer_to_message < 0)) {
                return false;
            }

            $newMessage = $this->db_connector->prepare("INSERT INTO messages (user_id, message_text, file_url, date_create, answer_regist_id) VALUES (:user_id, :message_text, :file_url, :date_create, :answer_regist_id)");
            $asResult = $newMessage->execute([":user_id" => $user_id, ":message_text" => $message_text, ":file_url" => $file_url ?? null, ":date_create" => date("Y-m-d H:i:m"), ":answer_regist_id" => $answer_to_message ?? null]);

            return ($asResult === false) ? false : true;
        }

        // Not available now
        // public function addAnswerToMessage(int $user_id, string $message_text, string $file_url = null, int $answer_to_message = null): bool {}

        public function getUserInfo(int $user_id): FeedbackUserData|bool {
            if ($user_id <= 0) {
                return false;
            }

            $getUserPrepare = $this->db_connector->prepare("SELECT * FROM usersbot WHERE user_id = :user_id");
            $asResult = $getUserPrepare->execute([":user_id"=> $user_id]);

            if ($asResult) {
                return new FBDB($getUserPrepare->fetch(\PDO::FETCH_ASSOC));
            }
            else {
                return false;
            }
        }
        public function banUser(int $user_id): bool {
            if ($user_id <= 0) {
                return false;
            }

            $banUserPrepare = $this->db_connector->prepare("UPDATE usersbot SET is_banned = 1 WHERE user_id = :user_id");
            $asResult = $banUserPrepare->execute([":user_id" => $user_id]);

            return ($asResult === false) ? false : true;
        }

        public function dropMessage(int $regist_message_id): bool {
            if ($regist_message_id <= 0) {
                return false;
            }

            $dropMessage = $this->db_connector->prepare("UPDATE messages SET is_dropped = 1 WHERE regist_message_id = :regist_message_id");
            $asResult = $dropMessage->execute([":regist_message_id" => $regist_message_id]);

            return $asResult;
        }
        public function dropAllMessagesFromUser(int $user_id): bool {
            if ($user_id <= 0) {
                return false;
            }

            $dropAllMessages = $this->db_connector->prepare("UPDATE messages SET is_dropped = 1 WHERE user_id = :user_id");
            $asResult = $dropAllMessages->execute([":user_id" => $user_id]);

            return $asResult;
        }
    }
?>