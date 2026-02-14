<?php
require '../../../vendor/autoload.php';
use Application\Mail;
use Application\Page;


// ... (Database connection same as index.php) ...

$mail = new Mail($pdo);
$page = new Page();

$uri = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($uri, '/'));
$id = end($parts);

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
    $item = $mail->getMail($id);
    if ($item) {
        $page->item($item);
    } else {
        $page->notFound();
    }
} elseif ($method === 'PUT') {
    $data = json_decode(file_get_contents("php://input"), true);
    $mail->updateMail($id, $data['subject'], $data['body']);
    $page->item(["message" => "Updated"]);
} elseif ($method === 'DELETE') {
    $mail->deleteMail($id);
    $page->item(["message" => "Deleted"]);
}