<?php
// Error 500 Diagnostic Tool
// Este script é projetado para diagnosticar e resolver erros 500 em servidores Apache com PHP
// IMPORTANTE: REMOVA ESTE ARQUIVO APÓS O USO!

// Mostrar todos os erros para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Função para verificar e corrigir arquivos de log
function check_and_fix_logs($dirPath, $files) {
    $results = [];
    
    // Verificar se o diretório existe
    if (!is_dir($dirPath)) {
        $results[] = "Diretório $dirPath não existe. Tentando criar...";
        if (mkdir($dirPath, 0755, true)) {
            $results[] = "✅ Diretório $dirPath criado com sucesso.";
        } else {
            $results[] = "❌ ERRO: Não foi possível criar o diretório $dirPath.";
            return $results;
        }
    }
    
    // Verificar permissões do diretório
    if (!is_writable($dirPath)) {
        $results[] = "❌ ERRO: O diretório $dirPath não tem permissão de escrita.";
        // Tentar corrigir permissões
        if (@chmod($dirPath, 0755)) {
            $results[] = "✅ Permissões corrigidas para o diretório $dirPath.";
        } else {
            $results[] = "❌ Não foi possível corrigir as permissões do diretório.";
        }
    } else {
        $results[] = "✅ Diretório $dirPath tem permissão de escrita.";
    }
    
    // Verificar cada arquivo
    foreach ($files as $file) {
        $filePath = $dirPath . '/' . $file;
        $results[] = "Verificando $file:";
        
        $needsRepair = false;
        
        if (!file_exists($filePath)) {
            $results[] = "  - Arquivo não existe.";
            $needsRepair = true;
        } else {
            $results[] = "  - Arquivo existe.";
            
            // Verificar permissões
            if (!is_readable($filePath)) {
                $results[] = "  - ❌ Arquivo não é legível.";
                // Tentar corrigir
                if (@chmod($filePath, 0644)) {
                    $results[] = "  - ✅ Permissões de leitura corrigidas.";
                } else {
                    $results[] = "  - ❌ Não foi possível corrigir permissões de leitura.";
                }
            }
            
            if (!is_writable($filePath)) {
                $results[] = "  - ❌ Arquivo não é gravável.";
                // Tentar corrigir
                if (@chmod($filePath, 0644)) {
                    $results[] = "  - ✅ Permissões de escrita corrigidas.";
                } else {
                    $results[] = "  - ❌ Não foi possível corrigir permissões de escrita.";
                }
            }
            
            // Verificar conteúdo
            $content = @file_get_contents($filePath);
            if ($content === false) {
                $results[] = "  - ❌ Não foi possível ler o conteúdo.";
                $needsRepair = true;
            } else {
                if (trim($content) === '') {
                    $results[] = "  - ❌ Arquivo vazio.";
                    $needsRepair = true;
                } else {
                    // Verificar se é JSON válido
                    $json = json_decode($content);
                    if (json_last_error() !== JSON_ERROR_NONE) {
                        $results[] = "  - ❌ JSON inválido: " . json_last_error_msg();
                        $needsRepair = true;
                    } else {
                        $results[] = "  - ✅ JSON válido.";
                    }
                }
            }
        }
        
        // Reparar arquivo se necessário
        if ($needsRepair) {
            $results[] = "  - Tentando reparar $file...";
            if (@file_put_contents($filePath, '[]')) {
                $results[] = "  - ✅ Arquivo reparado com sucesso.";
            } else {
                $results[] = "  - ❌ Não foi possível reparar o arquivo.";
            }
        }
    }
    
    return $results;
}

// Função para verificar o .htaccess
function check_htaccess($path) {
    $results = [];
    
    if (!file_exists($path)) {
        $results[] = "❌ ERRO: .htaccess não existe em $path";
        return $results;
    }
    
    if (!is_readable($path)) {
        $results[] = "❌ ERRO: .htaccess em $path não é legível.";
        return $results;
    }
    
    $results[] = "✅ .htaccess existe e é legível em $path";
    
    // Verificar conteúdo básico
    $content = @file_get_contents($path);
    if ($content === false) {
        $results[] = "❌ Não foi possível ler o conteúdo do .htaccess";
        return $results;
    }
    
    // Verificar características importantes
    if (strpos($content, 'RewriteEngine On') !== false) {
        $results[] = "✅ RewriteEngine está ativado";
    } else {
        $results[] = "❌ RewriteEngine não está ativado";
    }
    
    return $results;
}

// Função para verificar e corrigir problemas comuns que causam o erro 500
function diagnose_error_500($rootPath) {
    $results = [];
    
    // 1. Verificar conexão com arquivos de log
    $results[] = "<h2>Verificando arquivos de log</h2>";
    $logFiles = ['tickets.txt', 'user_registrations.txt', 'password_reset_tokens.txt', 'quick_users.txt', 'chat_1.txt'];
    $logResults = check_and_fix_logs($rootPath . '/logs', $logFiles);
    $results = array_merge($results, $logResults);
    
    // 2. Verificar diretório de uploads
    $results[] = "<h2>Verificando diretório de uploads</h2>";
    $uploadResults = check_and_fix_logs($rootPath . '/public/uploads', []);
    $results = array_merge($results, $uploadResults);
    
    // 3. Verificar arquivos .htaccess
    $results[] = "<h2>Verificando arquivos .htaccess</h2>";
    $htaccessResults = check_htaccess($rootPath . '/.htaccess');
    $results = array_merge($results, $htaccessResults);
    
    $htaccessPublicResults = check_htaccess($rootPath . '/public/.htaccess');
    $results = array_merge($results, $htaccessPublicResults);
    
    // 4. Verificar autoload.php
    $results[] = "<h2>Verificando autoload.php</h2>";
    $autoloadPath = $rootPath . '/vendor/autoload.php';
    if (!file_exists($autoloadPath)) {
        $results[] = "❌ ERRO: autoload.php não existe.";
    } else if (!is_readable($autoloadPath)) {
        $results[] = "❌ ERRO: autoload.php não é legível.";
    } else {
        $results[] = "✅ autoload.php existe e é legível.";
    }
    
    // 5. Verificar extensões PHP importantes
    $results[] = "<h2>Verificando extensões PHP necessárias</h2>";
    $requiredExtensions = ['json', 'fileinfo', 'mbstring'];
    foreach ($requiredExtensions as $ext) {
        if (extension_loaded($ext)) {
            $results[] = "✅ Extensão $ext está carregada.";
        } else {
            $results[] = "❌ ERRO: Extensão $ext não está carregada.";
        }
    }
    
    // 6. Testar criação de arquivo temporário
    $results[] = "<h2>Testando criação de arquivo temporário</h2>";
    $tempFile = tempnam(sys_get_temp_dir(), 'hd_');
    if ($tempFile !== false) {
        $results[] = "✅ Arquivo temporário criado: $tempFile";
        if (@unlink($tempFile)) {
            $results[] = "✅ Arquivo temporário removido com sucesso.";
        } else {
            $results[] = "❌ Não foi possível remover o arquivo temporário.";
        }
    } else {
        $results[] = "❌ ERRO: Não foi possível criar um arquivo temporário.";
    }
    
    return $results;
}

// Executar diagnóstico e mostrar resultados
echo "<html><head><title>Diagnóstico de Erro 500 - HelpDesk</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
    h1, h2 { color: #333; }
    .error { color: #d9534f; }
    .success { color: #5cb85c; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
</style></head><body>";

echo "<h1>Diagnóstico de Erro 500 - HelpDesk</h1>";
echo "<p>Este script ajuda a identificar e corrigir problemas que podem causar o erro 500 no servidor.</p>";

// Obter o diretório raiz
$rootPath = dirname(__FILE__);
echo "<p>Diretório raiz: $rootPath</p>";

$results = diagnose_error_500($rootPath);

// Exibir resultados
echo "<h2>Resultado do Diagnóstico</h2>";
echo "<div class='results'>";
foreach ($results as $result) {
    if (strpos($result, '❌') !== false) {
        echo "<div class='error'>$result</div>";
    } else if (strpos($result, '✅') !== false) {
        echo "<div class='success'>$result</div>";
    } else if (strpos($result, '<h2>') !== false) {
        echo $result;
    } else {
        echo "<div>$result</div>";
    }
}
echo "</div>";

echo "<h2>Informações do Sistema</h2>";
echo "<pre>";
echo "PHP Version: " . phpversion() . "\n";
echo "Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "\n";
echo "Server Name: " . ($_SERVER['SERVER_NAME'] ?? 'Unknown') . "\n";
echo "Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "\n";
echo "Script Filename: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'Unknown') . "\n";
echo "PHP SAPI: " . php_sapi_name() . "\n";
echo "OS: " . PHP_OS . "\n";
echo "Current User: " . get_current_user() . "\n";
echo "</pre>";

echo "<h2>Recomendação de Segurança</h2>";
echo "<p><strong>IMPORTANTE:</strong> Após resolver os problemas, remova este arquivo do servidor por questões de segurança.</p>";

echo "</body></html>";
?>
