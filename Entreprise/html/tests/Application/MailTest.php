<?php

use PHPUnit\Framework\TestCase;
use Application\Mail;

class MailTest extends TestCase {
    protected \PDO $pdo;

    protected function setUp(): void
    {
        $dsn = "pgsql:host=" . getenv('DB_TEST_HOST') . ";dbname=" . getenv('DB_TEST_NAME');
        $this->pdo = new \PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'));
        $this->pdo->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);

        // Clean and reinitialize the table
        $this->pdo->exec("DROP TABLE IF EXISTS mail;");
        $this->pdo->exec("
            CREATE TABLE mail (
                id SERIAL PRIMARY KEY,
                subject TEXT NOT NULL,
                body TEXT NOT NULL
            );
        ");
    }

    public function testCreateMail() {
        $mail = new Mail($this->pdo);
        $id = $mail->createMail("Alice", "Hello world");

        $this->assertIsInt($id);
        $this->assertEquals(1, $id);
    }

    public function testGetAllMail() {
        $mail = new Mail($this->pdo);

        $mail->createMail("Subject 1", "Body 1");
        $mail->createMail("Subject 2", "Body 2");

        $result = $mail->getAllMail();

        $this->assertIsArray($result);
        $this->assertCount(2, $result);
        $this->assertEquals("Subject 1", $result[0]['subject']);
    }

    public function testUpdateMail() {
        $mail = new Mail($this->pdo);

        $id = $mail->createMail("Old Subject", "Old Body");
        $mail->updateMail($id, "New Subject", "New Body");

        $updatedMail = $mail->getMail($id);

        $this->assertEquals("New Subject", $updatedMail['subject']);
        $this->assertEquals("New Body", $updatedMail['body']);
    }

    public function testDeleteMail() {
        $mail = new Mail($this->pdo);

        $id = $mail->createMail("To Delete", "Goodbye");
        $mail->deleteMail($id);

        $result = $mail->getMail($id);
        $this->assertFalse($result);
    }
}