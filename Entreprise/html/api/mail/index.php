<?php

require '../../vendor/autoload.php';

use Application\Mail;
use Application\Page;           
use PDOException;
use Exception;

$dsn = "pgsql:host=" . getenv('DB_PROD_HOST') . ";dbname=" . getenv('DB_PROD_NAME');

try {
    $pdo = new PDO($dsn, getenv('DB_USER'), getenv('DB_PASS'), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION
    ]);
} catch (PDOException $e) {
    
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed: " . $e->getMessage()]);
    exit;
}

$mail = new Mail($pdo);
$page = new Page();

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $page->list($mail->getAllMail());
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Read the raw JSON input
    $json = file_get_contents("php://input");
    $data = json_decode($json, true);

    // Validate input
    if (isset($data['subject']) && isset($data['body'])) {
        try {
            $id = $mail->createMail($data['subject'], $data['body']);
            // Return the ID
            http_response_code(201);
            echo json_encode(["id" => $id]);
        } catch (Exception $e) {
            http_response_code(500);
            echo json_encode(["error" => $e->getMessage()]);
        }
    } else {
        $page->badRequest();
    }
    exit;
}

// If neither GET nor POST...
$page->badRequest();