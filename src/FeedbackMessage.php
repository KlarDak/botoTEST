<?php

    namespace KlarDak\FeedbackDB;

    class FeedbackMessage {
        public int $regist_message_id;
        public int $user_id;
        public ?int $message_id;
        public string $message_text;
        public ?string $file_url;
        public string $date_created;
        public int $is_answer;
        public ?int $answer_message_regist;
        protected int $is_dropped;

        public function __construct(array $userInfo) {
            foreach ($userInfo as $key => $value) { 
                $this->{$key} = $value;
            }
        }
    }