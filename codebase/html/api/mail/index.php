<?php
require  __DIR__ . '/../../../autoload.php';

use Application\Mail;
use Application\Database;
use Application\Page;
use Application\Verifier;

$database = new Database('prod');
$page = new Page();

$mail = new Mail($database->getDb());

$verifier = new Verifier();
$verifier->decode($_SERVER['HTTP_AUTHORIZATION'] ?? '');

if (empty($verifier->userId) || empty($verifier->role)) {
    http_response_code(401);
    header('Content-Type: application/json');
    echo json_encode(array("error" => "Unauthorized"));
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);
    if (array_key_exists('name', $data) && array_key_exists('message', $data)) {
        $id = $mail->createMail($data['name'], $data['message'], $verifier->userId);
        $page->item(array("id" => $id));
    } else {
        $page->badRequest();
    }
} else if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    $page->item($mail->listMail($verifier->userId, $verifier->role));
} else {
    $page->badRequest();
}