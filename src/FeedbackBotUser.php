<?php

    namespace KlarDak\FeedbackDB;

    use KlarDak\FeedbackDB\Types\FeedbackConnector;
    use KlarDak\FeedbackDB\Types\FeedbackConnectorPDO;
    use KlarDak\FeedbackDB\Types\FeedbackUserInfo;

    class FeedbackBotUser extends FeedbackUserInfo {
        
        /**
         * Коннектор для базы данных (по прототипу FeedbackConnector)
         * @var FeedbackConnector
         */
        public FeedbackConnector $feedbackConnector;

        /**
         * Доступные к изменению методы
         * @var array
         */
        protected array $availableMethods = [
            "user_id",
            "username",
            "date_registration",
            "is_admin",
            "replied_messages",
            "is_banned",
            "is_dropped"
        ];

        public function __construct(FeedbackConnector $DBConnector, int $user_id) {
            $this->feedbackConnector = $DBConnector;

            $this->user_id = $user_id ?? 0;
        }

        /**
         * Проверяет, существует ли пользователь в базе данных
         * @return bool
         */
        public function isUserCreated() : bool {
            $isUserCreatedPrepare = "SELECT EXISTS (SELECT 1 FROM usersbot WHERE user_id = :user_id AND is_dropped = 0) AS row_exists";
            $isUserCreatedParams = [
                ":user_id" => $this->user_id
            ];

            $asResult = $this->feedbackConnector->returnCount($isUserCreatedPrepare, $isUserCreatedParams);

            return $asResult;
        }

        public function setUserInfo(): bool {
            $userInfoQuery = "SELECT * FROM usersbot WHERE user_id = :user_id";
            $userInfoParam = [
                ":user_id" => $this->user_id
            ];

            $userInfo = $this->feedbackConnector->returnArrayPrepare($userInfoQuery, $userInfoParam);

            $this->make($userInfo[0] ?? []);

            return true;
        }

        /**
         * Создание пользователя
         * @param int $user_id ID account
         * @param string $username link for user's account
         * @param string $name User's name
         * @return bool
         */
        public function createUser(int $user_id, string $username) : bool {
            if ($user_id > 0 && trim(strlen($username)) > 0) {
                $this->user_id = $user_id;
                
                $createUserPrepare = "INSERT INTO usersbot (user_id, username, date_registration, replied_messages) VALUES (:user_id, :username, NOW(), 0)";
                $createUserParams = [
                    ":user_id" => $user_id,
                    ":username" => $username,
                ];

                $asResult = $this->feedbackConnector->returnBoolPrepare($createUserPrepare, $createUserParams);

                if ($asResult) {
                    return $this->setUserInfo();
                }
            }

            return false;
        }

        /**
         * Блокировка пользователя
         * @return bool
         */
        public function banUser() : bool {
            return $this->feedbackConnector->returnBoolQuery(
                "UPDATE usersbot SET is_banned = 1 WHERE user_id = {$this->user_id}"
            );
        }

        /**
         * Разблокировка пользователя
         * @return bool
         */
        public function unbanUser() : bool {

            return $this->feedbackConnector->returnBoolQuery(
                "UPDATE usersbot SET is_banned = 0 WHERE user_id = {$this->user_id}"
            );
        }

        /**
         * Удаление аккаунта пользователя (невозвратное действие)
         * @return bool
         */
        public function dropUser() : bool {
            return $this->feedbackConnector->returnBoolQuery(
                "UPDATE usersbot SET is_dropped = 1 WHERE user_id = {$this->user_id}"
            );
        }
        
        /**
         * Update link to user account in database
         * @param string $username link for account
         * @return bool
         */
        public function setUsername(string $username) : bool {
            $setUsernamePrepare = "UPDATE usersbot SET username = :username WHERE user_id = {$this->user_id}";
            $setUsernameParams = [
                ":username" => $username
            ];

            return $this->feedbackConnector->returnBoolPrepare($setUsernamePrepare, $setUsernameParams);
        }
        // public function setName(string $name) : bool {}
        /**
         * Update admin rules for selected user
         * @param bool $is_admin
         * @return bool
         */
        public function setAdmin(bool $is_admin): bool {
            return $this->feedbackConnector->returnBoolPrepare(
                "UPDATE usersbot SET is_admin = :is_admin WHERE user_id = {$this->user_id}",
                [
                    "is_admin" => intval($is_admin)
                ]
            );
        }

        public function setRepliedMessages(int $count): bool {
            return $this->feedbackConnector->returnBoolQuery(
                "UPDATE usersbot SET replied_messages = replied_messages + {$count} WHERE user_id = {$this->user_id}"
            );
        }
        public function availableModifyMethods() : array {
            return $this->availableMethods;
        }
    }