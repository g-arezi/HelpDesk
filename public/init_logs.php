<?php
// Este script inicializa arquivos de log se não existirem ou estiverem corrompidos
// CUIDADO: Execute apenas uma vez, confira depois e então remova este arquivo

// Ativar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Inicializando Arquivos de Log do HelpDesk</h1>";

// Lista de arquivos de log necessários
$logFiles = [
    'tickets.txt',
    'user_registrations.txt',
    'password_reset_tokens.txt', 
    'quick_users.txt',
    'chat_1.txt'
];

$logDir = __DIR__ . '/../logs/';
echo "Diretório de logs: $logDir<br>";

// Verificar se o diretório existe, caso contrário, tente criá-lo
if (!is_dir($logDir)) {
    echo "Diretório de logs não existe. Tentando criar...<br>";
    if (mkdir($logDir, 0755, true)) {
        echo "<span style='color:green'>Diretório de logs criado com sucesso.</span><br>";
    } else {
        die("<span style='color:red'>ERRO: Não foi possível criar o diretório de logs!</span>");
    }
}

// Verificar permissões do diretório
if (!is_writable($logDir)) {
    die("<span style='color:red'>ERRO: O diretório de logs não tem permissão de escrita!</span>");
}

// Inicializar cada arquivo de log com um array JSON vazio
foreach ($logFiles as $file) {
    $filePath = $logDir . $file;
    echo "Verificando $file: ";
    
    $needsInit = false;
    
    // Verificar se o arquivo existe
    if (!file_exists($filePath)) {
        echo "Arquivo não existe. ";
        $needsInit = true;
    } else {
        echo "Arquivo existe. ";
        // Verificar se é um arquivo válido
        $content = @file_get_contents($filePath);
        if ($content === false) {
            echo "Não foi possível ler o arquivo. ";
            $needsInit = true;
        } elseif (trim($content) === '') {
            echo "Arquivo vazio. ";
            $needsInit = true;
        } else {
            // Verificar se é um JSON válido
            $json = json_decode($content);
            if (json_last_error() !== JSON_ERROR_NONE) {
                echo "JSON inválido: " . json_last_error_msg() . ". ";
                $needsInit = true;
            } else {
                echo "<span style='color:green'>Arquivo válido.</span><br>";
                continue;
            }
        }
    }
    
    // Inicializar o arquivo se necessário
    if ($needsInit) {
        echo "Tentando inicializar... ";
        if (file_put_contents($filePath, '[]')) {
            echo "<span style='color:green'>Arquivo inicializado com sucesso.</span><br>";
        } else {
            echo "<span style='color:red'>FALHA ao inicializar o arquivo!</span><br>";
        }
    }
}

// Verificar diretório de uploads
$uploadsDir = __DIR__ . '/uploads/';
echo "<h2>Verificando diretório de uploads</h2>";
echo "Diretório: $uploadsDir<br>";

if (!is_dir($uploadsDir)) {
    echo "Diretório de uploads não existe. Tentando criar...<br>";
    if (mkdir($uploadsDir, 0755, true)) {
        echo "<span style='color:green'>Diretório de uploads criado com sucesso.</span><br>";
    } else {
        echo "<span style='color:red'>ERRO: Não foi possível criar o diretório de uploads!</span><br>";
    }
} else {
    echo "Diretório de uploads existe. ";
    if (is_writable($uploadsDir)) {
        echo "<span style='color:green'>É gravável.</span><br>";
    } else {
        echo "<span style='color:red'>AVISO: O diretório de uploads não tem permissão de escrita!</span><br>";
    }
}

echo "<h2>Verificação concluída</h2>";
echo "Após verificar os resultados acima, remova este arquivo por segurança.";
?>
