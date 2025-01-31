<?php

    namespace KlarDak\FeedbackDB\Types;

    use KlarDak\FeedbackDB\Types\FeedbackConnector;

    class FeedbackUserInfo {
        protected FeedbackConnector $feedbackDBConnector;
        public int $user_id;
        public string $username;
        public string $name;
        public string $date_registration;
        public bool $is_admin;
        public int $total_messages;
        public int $replied_messages;
        public bool $is_banned;
        protected $is_dropped;

        function __construct(array $userInfo) {
            foreach ($userInfo as $key => $value) {
                $this->{$key} = $value ?? null;
            }
        }
    }