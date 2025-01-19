<?php
    namespace KlarDak\FeedbackDB;

    class FeedbackMessage {
        public int $regist_message_id;
        public int $user_id;
        public string $message_text;
        public ?string $file_url;
        public string $date_create;
        public bool $is_answer;
        public ?int $answer_regist_id;
        public bool $is_dropped;
        
        public function __construct(array $userMessage) {
            foreach ($userMessage as $keyMessage => $valueMessage) {
                $this->{$keyMessage} = $valueMessage ?? null;
            }
        }
    }
