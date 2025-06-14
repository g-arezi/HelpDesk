<?php
// Ativar exibição de erros para depuração
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Este arquivo verifica se o servidor atende aos requisitos mínimos para executar o HelpDesk
echo "<h1>Verificação de Compatibilidade do HelpDesk</h1>";

// Verificar logs de erros
echo "<h2>Logs de Erro</h2>";
echo "Caminho do log de erros do PHP: " . ini_get('error_log') . "<br>";
echo "Exibição de erros: " . (ini_get('display_errors') ? "Ativada" : "Desativada") . "<br>";
echo "Nível de erro: " . ini_get('error_reporting') . "<br>";

$minPhpVersion = '7.2.0';
echo "<h2>Versão do PHP</h2>";
echo "Versão necessária: $minPhpVersion<br>";
echo "Versão instalada: " . phpversion() . "<br>";
if (version_compare(phpversion(), $minPhpVersion, '>=')) {
    echo "<span style='color:green'>✓ OK</span>";
} else {
    echo "<span style='color:red'>✗ A versão do PHP precisa ser atualizada</span>";
}

echo "<h2>Permissões de Diretórios</h2>";
$dirsToCheck = [
    'logs' => '../logs',
    'uploads' => 'public/uploads'
];

foreach ($dirsToCheck as $name => $dir) {
    echo "<strong>$name</strong>: ";
    if (file_exists($dir)) {
        if (is_writable($dir)) {
            echo "<span style='color:green'>✓ Gravável</span>";
        } else {
            echo "<span style='color:red'>✗ Não gravável</span>";
        }
    } else {
        echo "<span style='color:red'>✗ Diretório não encontrado</span>";
    }
    echo "<br>";
}

echo "<h2>Extensões do PHP</h2>";
$extensions = [
    'json' => 'Necessário para armazenamento de dados',
    'fileinfo' => 'Necessário para upload de arquivos',
    'mbstring' => 'Necessário para manipulação de strings UTF-8'
];

foreach ($extensions as $ext => $desc) {
    echo "$ext: $desc - ";
    if (extension_loaded($ext)) {
        echo "<span style='color:green'>✓ Carregada</span>";
    } else {
        echo "<span style='color:red'>✗ Não carregada</span>";
    }
    echo "<br>";
}

echo "<h2>Configuração do PHP</h2>";
$configs = [
    'file_uploads' => 'Uploads de arquivos habilitado',
    'post_max_size' => 'Tamanho máximo de POST',
    'upload_max_filesize' => 'Tamanho máximo de upload',
    'memory_limit' => 'Limite de memória'
];

foreach ($configs as $config => $desc) {
    echo "$desc: " . ini_get($config) . "<br>";
}

echo "<h2>Variáveis de Ambiente</h2>";
echo "SERVER_NAME: " . $_SERVER['SERVER_NAME'] . "<br>";
echo "DOCUMENT_ROOT: " . $_SERVER['DOCUMENT_ROOT'] . "<br>";
echo "SCRIPT_FILENAME: " . $_SERVER['SCRIPT_FILENAME'] . "<br>";
echo "HTTP_HOST: " . $_SERVER['HTTP_HOST'] . "<br>";
echo "HTTPS: " . (isset($_SERVER['HTTPS']) ? $_SERVER['HTTPS'] : 'off') . "<br>";

echo "<h2>Resultado Final</h2>";
echo "Verifique os resultados acima e corrija quaisquer problemas antes de prosseguir.";

echo "<h2>Verificação de Arquivos de Log</h2>";
// Verificar se os arquivos de log existem e têm conteúdo JSON válido
$logFiles = [
    'tickets.txt',
    'user_registrations.txt',
    'password_reset_tokens.txt',
    'quick_users.txt',
    'chat_1.txt'
];

$logPath = __DIR__ . '/logs/';
echo "Caminho dos logs: " . $logPath . "<br>";

foreach ($logFiles as $file) {
    $fullPath = $logPath . $file;
    echo "Verificando $file: ";
    if (file_exists($fullPath)) {
        echo "Arquivo existe. ";
        if (is_readable($fullPath)) {
            echo "É legível. ";
            $content = file_get_contents($fullPath);
            if ($content === false) {
                echo "<span style='color:red'>Erro ao ler o conteúdo.</span>";
            } else {
                echo "Tamanho: " . strlen($content) . " bytes. ";
                if (empty($content)) {
                    echo "<span style='color:orange'>Arquivo vazio.</span>";
                } else {
                    // Verificar se é JSON válido
                    $json = json_decode($content);
                    if (json_last_error() === JSON_ERROR_NONE) {
                        echo "<span style='color:green'>JSON válido.</span>";
                    } else {
                        echo "<span style='color:red'>JSON inválido: " . json_last_error_msg() . "</span>";
                    }
                }
            }
        } else {
            echo "<span style='color:red'>Não é legível.</span>";
        }
    } else {
        echo "<span style='color:red'>Arquivo não existe.</span>";
    }
    echo "<br>";
}

echo "<h2>Verificação de Permissões de Diretórios</h2>";
// Verificar permissões de diretórios críticos
$directories = [
    'logs' => __DIR__ . '/logs',
    'public' => __DIR__ . '/public',
    'public/uploads' => __DIR__ . '/public/uploads',
    'vendor' => __DIR__ . '/vendor'
];

foreach ($directories as $name => $dir) {
    echo "Diretório $name: ";
    if (file_exists($dir)) {
        echo "Existe. ";
        if (is_dir($dir)) {
            echo "É um diretório. ";
            if (is_readable($dir)) {
                echo "Legível. ";
            } else {
                echo "<span style='color:red'>Não legível.</span> ";
            }
            if (is_writable($dir)) {
                echo "Gravável. ";
            } else {
                echo "<span style='color:red'>Não gravável.</span> ";
            }
            echo "Permissões: " . substr(sprintf('%o', fileperms($dir)), -4);
        } else {
            echo "<span style='color:red'>Não é um diretório.</span>";
        }
    } else {
        echo "<span style='color:red'>Não existe.</span>";
    }
    echo "<br>";
}

echo "<h2>Verificação de arquivo .htaccess</h2>";
// Verificar se o .htaccess existe e seu conteúdo
$htaccess = __DIR__ . '/.htaccess';
if (file_exists($htaccess)) {
    echo ".htaccess na raiz: Existe. ";
    if (is_readable($htaccess)) {
        echo "É legível.<br>";
        echo "Primeiras 10 linhas:<br>";
        echo "<pre>";
        $lines = file($htaccess, FILE_IGNORE_NEW_LINES);
        for ($i = 0; $i < min(10, count($lines)); $i++) {
            echo htmlspecialchars($lines[$i]) . "\n";
        }
        echo "</pre>";
    } else {
        echo "<span style='color:red'>Não é legível.</span><br>";
    }
} else {
    echo "<span style='color:orange'>.htaccess na raiz: Não existe.</span><br>";
}

$htaccessPublic = __DIR__ . '/public/.htaccess';
if (file_exists($htaccessPublic)) {
    echo ".htaccess na pasta public: Existe. ";
    if (is_readable($htaccessPublic)) {
        echo "É legível.<br>";
        echo "Primeiras 10 linhas:<br>";
        echo "<pre>";
        $lines = file($htaccessPublic, FILE_IGNORE_NEW_LINES);
        for ($i = 0; $i < min(10, count($lines)); $i++) {
            echo htmlspecialchars($lines[$i]) . "\n";
        }
        echo "</pre>";
    } else {
        echo "<span style='color:red'>Não é legível.</span><br>";
    }
} else {
    echo "<span style='color:orange'>.htaccess na pasta public: Não existe.</span><br>";
}

echo "<h2>Teste de Criação de Arquivo Temporário</h2>";
// Testar se podemos criar um arquivo temporário
$tmpDir = sys_get_temp_dir();
echo "Diretório temporário: $tmpDir<br>";
$tmpFile = tempnam($tmpDir, 'hd_test_');
if ($tmpFile !== false) {
    echo "Arquivo temporário criado: $tmpFile<br>";
    if (unlink($tmpFile)) {
        echo "<span style='color:green'>Arquivo temporário removido com sucesso.</span><br>";
    } else {
        echo "<span style='color:red'>Não foi possível remover o arquivo temporário.</span><br>";
    }
} else {
    echo "<span style='color:red'>Não foi possível criar um arquivo temporário.</span><br>";
}

echo "<h2>Informações do Sistema</h2>";
echo "Sistema operacional: " . PHP_OS . "<br>";
echo "Interface do servidor: " . php_sapi_name() . "<br>";
echo "Usuário do PHP: " . get_current_user() . "<br>";
echo "Diretório atual: " . getcwd() . "<br>";
echo "Document root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Não disponível') . "<br>";
echo "Script filename: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'Não disponível') . "<br>";

echo "<h2>Verificação de autoload.php</h2>";
// Verificar se o autoload.php existe e funciona
$autoloadPath = __DIR__ . '/vendor/autoload.php';
echo "Caminho do autoload.php: $autoloadPath<br>";
if (file_exists($autoloadPath)) {
    echo "O arquivo existe. ";
    if (is_readable($autoloadPath)) {
        echo "É legível. ";
        try {
            // Tente carregar o autoload sem prejudicar o script
            include_once $autoloadPath;
            echo "<span style='color:green'>Carregado com sucesso.</span>";
        } catch (Exception $e) {
            echo "<span style='color:red'>Erro ao carregar: " . $e->getMessage() . "</span>";
        } catch (Error $e) {
            echo "<span style='color:red'>Erro ao carregar: " . $e->getMessage() . "</span>";
        }
    } else {
        echo "<span style='color:red'>Não é legível.</span>";
    }
} else {
    echo "<span style='color:red'>O arquivo não existe.</span>";
}
echo "<br>";
?>
