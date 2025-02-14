<?php
    namespace KlarDak\FeedbackDBTests;

    use KlarDak\FeedbackDB\FeedbackBotUser;
    use KlarDak\FeedbackDB\Types\FeedbackConnectorPDO;
    use KlarDak\FeedbackDB\Types\FeedbackConnector;
    use KlarDak\FeedbackDB\Types\FeedbackUserInfo;

    use PHPUnit\Framework\TestCase;

    class FeedbackBotUserTest extends TestCase {
        private static FeedbackConnector $feedbackConnector;
        private static FeedbackBotUser $feedbackBotUser;

        public static function setUpBeforeClass() : void {
            self::$feedbackConnector = new FeedbackConnectorPDO("mysql-8.0", "root", "", "feedback_messages");
            self::$feedbackConnector->getPDO()->beginTransaction();

            self::$feedbackBotUser = new FeedbackBotUser(self::$feedbackConnector, 0);
        }

        public static function tearDownAfterClass() : void { 
            self::$feedbackConnector->getPDO()->rollBack();
        }

        public function testIsUserCreated() : void {
            $asResult = (bool) self::$feedbackBotUser->isUserCreated();

            $this->assertFalse($asResult);
        }

        public function testCreateUser() : void {
            $asResult = (bool) self::$feedbackBotUser->createUser(
                user_id: 1,
                username: "TestUser",
            );

            $this->assertTrue($asResult);

            $checkRequest = $this->checkCountWith("user_id = 1 AND is_dropped = 0");

            $this->assertEquals(1, $checkRequest);
        }

        public function testBanUser() : void {
            $asResult = self::$feedbackBotUser->banUser();
            $this->assertTrue($asResult);

            $checkRequest = $this->checkCountWith("user_id = 1 AND is_banned = 1");
            $this->assertEquals(1, $checkRequest);
        }

        public function testUnbanUser() : void {
            $asResult = self::$feedbackBotUser->unbanUser();
            $this->assertTrue($asResult);

            $checkRequest = $this->checkCountWith("user_id = 1 AND is_banned = 0");
            $this->assertEquals(1, $checkRequest);
        }

        public function testSetUsername() : void {
            $newUsername = "NewTest";
            $asResult = self::$feedbackBotUser->setUsername(
                username: $newUsername
            );
            $this->assertTrue($asResult);

            $checkRequest = $this->checkCountWith("username = '{$newUsername}' AND user_id = 1");
            $this->assertEquals(1, $checkRequest);
        }

        public function testIsAdmin() : void {
            $is_admin = true;
            $asResult = self::$feedbackBotUser->setAdmin(
                is_admin: $is_admin
            );
            $this->assertTrue($asResult);

            $checkRequest = $this->checkCountWith("user_id = 1 AND is_admin = ". (int) $is_admin);
            $this->assertEquals(1, $checkRequest);
        }

        public function testSetRepliedMessages() : void {
            $setCount = 2;
            $totalMessages = self::$feedbackBotUser->setRepliedMessages($setCount);
            $this->assertTrue($totalMessages);

            $checkRequest = self::$feedbackConnector->returnArrayQuery("SELECT replied_messages FROM usersbot WHERE user_id = 1 AND is_dropped = 0");
            $this->assertEquals($setCount, $checkRequest[0]["replied_messages"]);
        }
        
        public function testDropUser() : void {
            $asResult = self::$feedbackBotUser->dropUser();
            $this->assertTrue($asResult);

            $checkRequest = $this->checkCountWith("is_dropped = 1 AND user_id = 1");
            $this->assertEquals(1, $checkRequest);
        }

        private function checkCountWith(string $query) : int {
            $checkUserInfo = self::$feedbackConnector->returnArrayQuery("SELECT EXISTS (SELECT 1 FROM usersbot WHERE {$query}) AS row_exists");
            return $checkUserInfo[0]["row_exists"];
        }
    }