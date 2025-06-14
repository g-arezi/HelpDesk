<?php
namespace App\Controller;

require_once __DIR__ . '/../../public/api_cors.php';

class TicketController
{
    public function open()
    {
        // Verificação de autenticação (secundária, já verificado em open.php)
        if (!isset($_SESSION['auth']) || $_SESSION['auth'] !== true) {
            header('Location: login.php?access=denied&redirect=' . urlencode($_SERVER['REQUEST_URI']));
            exit;
        }
        
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

            $produto = $_POST['produto'] ?? '';
            // Novos campos para filmes/séries
            $filme_nome = $_POST['filme_nome'] ?? '';
            $filme_tmdb = $_POST['filme_tmdb'] ?? '';
            $filme_obs = $_POST['filme_obs'] ?? '';
            $serie_nome = $_POST['serie_nome'] ?? '';
            $serie_tmdb = $_POST['serie_tmdb'] ?? '';
            $serie_obs = $_POST['serie_obs'] ?? '';
            $filmes_obse = $_POST['filmes_obse'] ?? '';
            $series_obse = $_POST['series_obse'] ?? '';

            $filmes_obse_label = '';
            if (isset($_POST['filmes_obse'])) {
                if ($_POST['filmes_obse'] === 'Solicitar conteúdo') {
                    $filmes_obse_label = '🆕- Solicitar conteúdo';
                } elseif ($_POST['filmes_obse'] === 'Corrigir conteúdo') {
                    $filmes_obse_label = '🛠️- Corrigir conteúdo';
                }
            }
            $series_obse_label = '';
            if (isset($_POST['series_obse'])) {
                if ($_POST['series_obse'] === 'Solicitar conteúdo') {
                    $series_obse_label = '🆕- Solicitar conteúdo';
                } elseif ($_POST['series_obse'] === 'Corrigir conteúdo') {
                    $series_obse_label = '🛠️- Corrigir conteúdo';
                }
            }

            $ticket = [
                'name' => $name,
                'email' => $email,
                'produto' => $produto,
                'subject' => $subject,
                'message' => $message,
                'imagePath' => $imagePath,
                'telefone' => $telefone,
                'status' => 'nao_aberto',
            ];
            // Adiciona campos extras se for filmes ou séries
            if ($produto === 'filmes') {
                $ticket['filme_nome'] = $filme_nome;
                $ticket['filme_tmdb'] = $filme_tmdb;
                $ticket['filme_obs'] = $filme_obs;
                $ticket['filmes_obse'] = $filmes_obse;
                $ticket['filmes_obse_label'] = $filmes_obse_label;
            } elseif ($produto === 'series') {
                $ticket['serie_nome'] = $serie_nome;
                $ticket['serie_tmdb'] = $serie_tmdb;
                $ticket['serie_obs'] = $serie_obs;
                $ticket['series_obse'] = $series_obse;
                $ticket['series_obse_label'] = $series_obse_label;
            }
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
