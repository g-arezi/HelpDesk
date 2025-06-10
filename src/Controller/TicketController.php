<?php
namespace App\Controller;

require_once __DIR__ . '/../../public/api_cors.php';

class TicketController
{
    public function open()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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

            // Exibe página de sucesso ao invés de JSON
            // Passa o ticket recém-criado para a view de sucesso
            $lastTicket = $ticket;
            include __DIR__ . '/../View/success.php';
            return;
        }
        include __DIR__ . '/../View/open_form.php';
    }
}
