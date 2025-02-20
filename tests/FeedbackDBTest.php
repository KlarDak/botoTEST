<?php
    namespace KlarDak\FeedbackDBTests;

    use KlarDak\FeedbackDB\FeedbackBotAdmin;
    use KlarDak\FeedbackDB\FeedbackBotUser;
    use KlarDak\FeedbackDB\FeedbackDB;
    use KlarDak\FeedbackDB\Types\FeedbackMessage;
    use KlarDak\FeedbackDB\Types\FeedbackConnector;
    use KlarDak\FeedbackDB\Types\FeedbackConnectorPDO;
    use KlarDak\FeedbackDB\Types\FeedbackUserInfo;
    use PHPUnit\Framework\TestCase;

    class FeedbackDBTest extends TestCase {
        private static FeedbackConnector $feedbackConnector;
        private static FeedbackDB $feedbackDB;

        public static function setUpBeforeClass(): void {
            self::$feedbackConnector = new FeedbackConnectorPDO("mysql-8.0", "root", "", "feedback_messages");
            self::$feedbackConnector->getPDO()->beginTransaction();

            self::$feedbackDB = new FeedbackDB(self::$feedbackConnector);
        }

        public static function tearDownAfterClass(): void {
            self::$feedbackConnector->getPDO()->rollBack();
            self::$feedbackConnector->returnBoolQuery("TRUNCATE TABLE messages");
        }

        public function testAddMessage() : void {
            $asResult = self::$feedbackDB->addMessage(
                message_text: "Test", 
                user_id: 0,
            );

            $this->assertTrue($asResult);
        }

        public function testAddReplyToMessage() : void {
            $asResult = self::$feedbackDB->addReplyToMessage(
                user_id: 1,
                regist_message_id: 1,
                message_text: "Test reply message"
            );

            $this->assertTrue($asResult);
        }

        public function testGetMessage() : void {
            $asResult = self::$feedbackDB->getMessage(
                regist_message_id: 1
            );

            $this->assertInstanceOf(FeedbackMessage::class, $asResult);
        }

        public function testGetCountOfUnreadMessages() : void {
            $asResult = self::$feedbackDB->getCountOfUnreadMessages();

            $this->assertIsInt($asResult);
        }

        public function testGetUnreadMessages() : void {
            $asResult = self::$feedbackDB->getUnreadMessages(
                count_messages: 1,
                page: 1
            );

            $this->assertInstanceOf(FeedbackMessage::class, $asResult[0]);
            $this->assertIsInt(1, $asResult[0]->regist_message_id);
        }

        public function testGetUser() : void {
            $asResult = self::$feedbackDB->getUser(1);

            $this->assertInstanceOf(FeedbackBotUser::class, $asResult);
            
            $this->assertEquals(null, $asResult->user_id);
        }

        public function testGetAdmins() : void {
            $asResult = self::$feedbackDB->getAdmins();

            $this->assertInstanceOf(FeedbackBotAdmin::class, $asResult);
        }

        public function testGetBannedUsers() : void {
            $asResult = self::$feedbackDB->getBannedUsers();

            $this->assertEquals(1, $asResult->asCount());
            $this->assertInstanceOf(FeedbackUserInfo::class, $asResult->asList()[0]);
            $this->assertEquals(1, $asResult->asList()[0]->user_id);

        }

        public function testDropMessage() : void {
            $asResult = self::$feedbackDB->dropMessage(
                regist_message_id: 1
            );

            $this->assertTrue($asResult);

            $checkResult = self::$feedbackConnector->returnCount(
                "SELECT EXISTS (SELECT 1 FROM messages WHERE regist_message_id = 1 AND is_dropped = 0)",
                []
            );

            $this->assertEquals(0, $checkResult);
        }
    }