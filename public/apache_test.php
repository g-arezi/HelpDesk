<?php
// apache_test.php - Teste específico para ambiente Apache
// IMPORTANTE: REMOVA ESTE ARQUIVO APÓS O USO!

// Mostrar todos os erros para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<html><head><title>Teste de Ambiente Apache</title>";
echo "<style>
    body { font-family: Arial, sans-serif; margin: 20px; line-height: 1.6; }
    h1, h2 { color: #333; }
    .error { color: #d9534f; }
    .success { color: #5cb85c; }
    pre { background: #f5f5f5; padding: 10px; border-radius: 5px; }
    table { border-collapse: collapse; width: 100%; }
    table, th, td { border: 1px solid #ddd; }
    th, td { padding: 8px; text-align: left; }
    th { background-color: #f2f2f2; }
</style></head><body>";

echo "<h1>Teste de Ambiente Apache</h1>";
echo "<p>Este script verifica configurações específicas do Apache que podem causar erro 500.</p>";

// Verificar se estamos rodando em um servidor Apache
echo "<h2>Servidor Web</h2>";
if (isset($_SERVER['SERVER_SOFTWARE']) && stripos($_SERVER['SERVER_SOFTWARE'], 'apache') !== false) {
    echo "<p class='success'>✅ Rodando em servidor Apache: " . $_SERVER['SERVER_SOFTWARE'] . "</p>";
} else {
    echo "<p class='error'>❌ Não identificado como Apache: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido') . "</p>";
}

// Verificar módulos do Apache importantes
echo "<h2>Módulos do Apache</h2>";
echo "<p>Nota: Nem todos os servidores disponibilizam esta informação.</p>";

$requiredModules = [
    'mod_rewrite' => 'Necessário para URL amigáveis e redirecionamentos',
    'mod_php' => 'Necessário para executar PHP',
    'mod_headers' => 'Necessário para cabeçalhos de segurança'
];

if (function_exists('apache_get_modules')) {
    $modules = apache_get_modules();
    echo "<table>";
    echo "<tr><th>Módulo</th><th>Descrição</th><th>Status</th></tr>";
    
    foreach ($requiredModules as $module => $description) {
        echo "<tr>";
        echo "<td>$module</td>";
        echo "<td>$description</td>";
        
        if (in_array($module, $modules)) {
            echo "<td class='success'>✅ Carregado</td>";
        } else {
            echo "<td class='error'>❌ Não carregado</td>";
        }
        
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p class='error'>❌ Função apache_get_modules() não disponível. Não é possível listar os módulos do Apache diretamente.</p>";
}

// Verificar variáveis do servidor importantes para o Apache
echo "<h2>Variáveis do Servidor</h2>";
$serverVars = [
    'DOCUMENT_ROOT' => 'Diretório raiz do documento',
    'SERVER_NAME' => 'Nome do servidor',
    'REQUEST_URI' => 'URI solicitada',
    'SCRIPT_FILENAME' => 'Caminho completo do script',
    'HTTP_HOST' => 'Host HTTP',
    'HTTPS' => 'Status HTTPS',
    'SERVER_PORT' => 'Porta do servidor',
    'REMOTE_ADDR' => 'Endereço IP do cliente',
    'SCRIPT_NAME' => 'Nome do script',
    'PHP_SELF' => 'Caminho do script'
];

echo "<table>";
echo "<tr><th>Variável</th><th>Descrição</th><th>Valor</th></tr>";

foreach ($serverVars as $var => $desc) {
    echo "<tr>";
    echo "<td>$var</td>";
    echo "<td>$desc</td>";
    echo "<td>" . (isset($_SERVER[$var]) ? htmlspecialchars($_SERVER[$var]) : 'Não definido') . "</td>";
    echo "</tr>";
}

echo "</table>";

// Verificar permissões de arquivo e diretório
echo "<h2>Permissões de Arquivos e Diretórios</h2>";

function check_permissions($path, $description) {
    echo "<p><strong>$description:</strong> ";
    
    if (!file_exists($path)) {
        echo "<span class='error'>❌ Não existe</span>";
        return;
    }
    
    if (is_dir($path)) {
        echo "Diretório ";
    } else {
        echo "Arquivo ";
    }
    
    echo "existe. ";
    
    // Verificar permissões
    $perms = fileperms($path);
    $octal = substr(sprintf('%o', $perms), -4);
    echo "Permissões: $octal. ";
    
    if (is_readable($path)) {
        echo "<span class='success'>Legível</span>. ";
    } else {
        echo "<span class='error'>Não legível</span>. ";
    }
    
    if (is_writable($path)) {
        echo "<span class='success'>Gravável</span>.";
    } else {
        echo "<span class='error'>Não gravável</span>.";
    }
    
    echo "</p>";
}

$pathsToCheck = [
    dirname(__FILE__) => "Diretório atual",
    dirname(dirname(__FILE__)) => "Diretório pai",
    dirname(dirname(__FILE__)) . '/logs' => "Diretório de logs",
    dirname(__FILE__) . '/uploads' => "Diretório de uploads",
    dirname(dirname(__FILE__)) . '/.htaccess' => "Arquivo .htaccess raiz",
    dirname(__FILE__) . '/.htaccess' => "Arquivo .htaccess public"
];

foreach ($pathsToCheck as $path => $desc) {
    check_permissions($path, $desc);
}

// Verificar configurações do PHP relevantes para Apache
echo "<h2>Configurações do PHP para Apache</h2>";

$phpSettings = [
    'display_errors' => 'Exibir erros',
    'error_reporting' => 'Nível de relatório de erros',
    'error_log' => 'Arquivo de log de erros',
    'log_errors' => 'Registrar erros no log',
    'upload_max_filesize' => 'Tamanho máximo de upload',
    'post_max_size' => 'Tamanho máximo de POST',
    'memory_limit' => 'Limite de memória',
    'max_execution_time' => 'Tempo máximo de execução',
    'disable_functions' => 'Funções desativadas'
];

echo "<table>";
echo "<tr><th>Configuração</th><th>Descrição</th><th>Valor</th></tr>";

foreach ($phpSettings as $setting => $desc) {
    echo "<tr>";
    echo "<td>$setting</td>";
    echo "<td>$desc</td>";
    echo "<td>" . ini_get($setting) . "</td>";
    echo "</tr>";
}

echo "</table>";

// Testar acesso aos arquivos essenciais
echo "<h2>Teste de Acesso a Arquivos Essenciais</h2>";

$essentialFiles = [
    dirname(dirname(__FILE__)) . '/vendor/autoload.php' => 'Autoload do Composer',
    dirname(dirname(__FILE__)) . '/logs/tickets.txt' => 'Arquivo de tickets',
    dirname(dirname(__FILE__)) . '/index.php' => 'Arquivo index raiz'
];

foreach ($essentialFiles as $file => $desc) {
    echo "<p><strong>$desc:</strong> ";
    
    if (file_exists($file)) {
        echo "<span class='success'>✅ Existe</span>. ";
        
        if (is_readable($file)) {
            echo "<span class='success'>É legível</span>.";
        } else {
            echo "<span class='error'>❌ Não é legível</span>.";
        }
    } else {
        echo "<span class='error'>❌ Não existe</span>.";
    }
    
    echo "</p>";
}

// Testar funções do PHP frequentemente desativadas
echo "<h2>Teste de Funções PHP</h2>";

$functions = [
    'file_get_contents' => 'Ler arquivos',
    'file_put_contents' => 'Escrever em arquivos',
    'json_encode' => 'Codificar JSON',
    'json_decode' => 'Decodificar JSON',
    'chmod' => 'Alterar permissões de arquivo',
    'mkdir' => 'Criar diretório',
    'unlink' => 'Excluir arquivo',
    'mail' => 'Enviar e-mail'
];

echo "<table>";
echo "<tr><th>Função</th><th>Propósito</th><th>Status</th></tr>";

foreach ($functions as $function => $purpose) {
    echo "<tr>";
    echo "<td>$function</td>";
    echo "<td>$purpose</td>";
    
    if (function_exists($function)) {
        echo "<td class='success'>✅ Disponível</td>";
    } else {
        echo "<td class='error'>❌ Não disponível</td>";
    }
    
    echo "</tr>";
}

echo "</table>";

echo "<h2>Recomendações de Segurança</h2>";
echo "<p><strong>IMPORTANTE:</strong> Remova este arquivo após o diagnóstico.</p>";
echo "<p>Em produção, defina <code>php_flag display_errors off</code> no .htaccess para proteger informações sensíveis.</p>";

echo "</body></html>";
?>
