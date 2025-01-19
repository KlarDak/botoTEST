<?php
    namespace KlarDak\FeedbackDB;

    class FeedbackUserData {
        public int $user_id;
        public string $username;
        public string $name;
        public string $date_registration;
        public bool $is_admin;
        public int $number_of_messages;
        public int $messages_with_answer;
        public bool $is_banned;
        public bool $is_dropped;

        public function __construct(array $userData) {
            foreach ($userData as $key_name => $value){
                $this->{$key_name} = $value ?? null;
            }
        }
    }