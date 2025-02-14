<?php
    namespace KlarDak\FeedbackDBTests;

    use KlarDak\FeedbackDB\FeedbackBotAdmin;
    use KlarDak\FeedbackDB\Types\FeedbackUserInfo;
    use PHPUnit\Framework\TestCase;
    use KlarDak\FeedbackDB\Types\FeedbackConnector;
    use KlarDak\FeedbackDB\Types\FeedbackConnectorPDO;

    class FeedbackBotAdminTest extends TestCase {
        private static FeedbackConnector $feedbackConnector;
        private static FeedbackBotAdmin $feedbackBotAdmin;

        public static function setUpBeforeClass() : void {
            self::$feedbackConnector = new FeedbackConnectorPDO("mysql-8.0", "root", "", "feedback_messages");
            self::$feedbackConnector->getPDO()->beginTransaction();

            self::$feedbackBotAdmin = new FeedbackBotAdmin(self::$feedbackConnector);
        }

        public static function tearDownAfterClass() : void { 
            self::$feedbackConnector->getPDO()->rollBack();
        }

        public function testIsList() : void {
            $getAdminsID = self::$feedbackBotAdmin->asList();

            $this->assertInstanceOf(FeedbackUserInfo::class, $getAdminsID[0]);
            $this->assertIsInt(7146440717, $getAdminsID[0]->is_admin);
        }

        public function testIsListID() : void {
            $getAdminsID = self::$feedbackBotAdmin->asListID();

            $this->assertIsArray($getAdminsID);
            $this->assertIsInt($getAdminsID[0]);
        }
    }

