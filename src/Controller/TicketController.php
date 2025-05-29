<?php
namespace App\Controller;

class TicketController
{
    public function open()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $email = $_POST['email'] ?? '';
            $subject = $_POST['subject'] ?? '';
            $message = $_POST['message'] ?? '';

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

            $ticket = new \App\Model\Ticket($name, $email, $subject, $message, $imagePath);
            // Aqui você pode salvar o ticket em banco de dados ou enviar por e-mail
            // Exemplo: salvar em arquivo temporário
            file_put_contents(__DIR__ . '/../../tickets.txt', json_encode($ticket) . PHP_EOL, FILE_APPEND);

            include __DIR__ . '/../View/success.php';
            return;
        }
        include __DIR__ . '/../View/open_form.php';
    }
}
