<?php
    namespace KlarDak\FeedbackDBTests;

    use KlarDak\FeedbackDB\FeedbackDB;
    use KlarDak\FeedbackDB\FeedbackMessage;
    use KlarDak\FeedbackDB\Types\FeedbackConnector;
    use KlarDak\FeedbackDB\Types\FeedbackConnectorPDO;
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

        public function testDropMessage() : void {
            $asResult = self::$feedbackDB->dropMessage(
                regist_message_id: 1
            );

            $this->assertTrue($asResult);

            $checkResult = self::$feedbackConnector->returnCount(
                "SELECT EXISTS (SELECT 1 FROM messages WHERE regist_message_id = 1 AND is_dropped = 0)"
            );

            $this->assertEquals(0, $checkResult);
        }
    }