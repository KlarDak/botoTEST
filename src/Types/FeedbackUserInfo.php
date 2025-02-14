<?php

    namespace KlarDak\FeedbackDB\Types;

    use KlarDak\FeedbackDB\Types\FeedbackConnector;

    class FeedbackUserInfo {
        protected FeedbackConnector $feedbackDBConnector;
        public ?int $user_id = null;
        public ?string $username = null;
        public ?string $date_registration = null;
        public ?bool $is_admin = null;
        public ?int $replied_messages = null;
        public ?bool $is_banned = null;
        protected ?int $is_dropped = null;

        public function make(array $userInfo) : void {
            $this->user_id = $userInfo["user_id"];
            $this->username = $userInfo["usernane"];
            $this->date_registration = $userInfo["date_registration"];
            $this->is_admin = $userInfo["is_admin"];
            $this->replied_messages = $userInfo["replied_messages"];
            $this->is_banned = $userInfo["is_banned"];
            $this->is_dropped = $userInfo["is_dropped"];
        }
    }