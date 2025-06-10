<?php
namespace App\Controller;

require_once __DIR__ . '/../../public/api_cors.php';

class TicketController
{
    public function open()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            header('Content-Type: application/json; charset=utf-8');
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $subject = $_POST['subject'] ?? '';
            $message = isset($_POST['message']) ? trim($_POST['message']) : '';
            $telefone = $_POST['telefone'] ?? '';

            $imagePath = null;
            if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
                $uploadDir = __DIR__ . '/../../public/uploads/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }
                $fileTmp = $_FILES['image']['tmp_name'];
                $fileName = uniqid('img_') . '_' . basename($_FILES['image']['name']);
                $filePath = $uploadDir . $fileName;
                if (move_uploaded_file($fileTmp, $filePath)) {
                    $imagePath = 'uploads/' . $fileName;
                }
            }

            $ticket = [
                'name' => $name,
                'email' => $email,
                'subject' => $subject,
                'message' => $message,
                'imagePath' => $imagePath,
                'telefone' => $telefone,
                'status' => 'nao_aberto'
            ];
            $file = __DIR__ . '/../../logs/tickets.txt';
            $tickets = [];
            if (file_exists($file)) {
                $content = file_get_contents($file);
                $tickets = json_decode($content, true) ?: [];
            }
            $tickets[] = $ticket;
            file_put_contents($file, json_encode($tickets, JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT));

            echo json_encode([
                'success' => true,
                'message' => 'Chamado criado com sucesso!',
                'ticket' => $ticket
            ], JSON_UNESCAPED_UNICODE | JSON_PRETTY_PRINT);
            return;
        }
        include __DIR__ . '/../View/open_form.php';
    }
}
