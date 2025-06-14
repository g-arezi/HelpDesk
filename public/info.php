<?php
// info.php - Arquivo para diagnóstico básico de problemas no servidor
// REMOVA ESTE ARQUIVO APÓS O DIAGNÓSTICO!

// Exibir erros para diagnóstico
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

echo "<h1>Informações de Depuração do PHP</h1>";

// Informações básicas do PHP
echo "<h2>Versão do PHP</h2>";
echo "PHP versão: " . phpversion();

// Verificar permissões de diretório
echo "<h2>Permissões</h2>";
echo "Diretório atual: " . getcwd() . "<br>";
echo "Usuário PHP: " . get_current_user() . "<br>";

// Testar escrita em diretório de logs
$logTest = __DIR__ . '/logs/test.txt';
echo "Testando escrita em logs: ";
$result = @file_put_contents($logTest, "Teste: " . date('Y-m-d H:i:s'));
if ($result !== false) {
    echo "<span style='color:green'>OK</span><br>";
    unlink($logTest);
} else {
    echo "<span style='color:red'>FALHA</span><br>";
}

// Informações do servidor
echo "<h2>Servidor</h2>";
echo "Server software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Desconhecido') . "<br>";
echo "Document root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Desconhecido') . "<br>";
echo "Script filename: " . ($_SERVER['SCRIPT_FILENAME'] ?? 'Desconhecido') . "<br>";
echo "HTTP Host: " . ($_SERVER['HTTP_HOST'] ?? 'Desconhecido') . "<br>";

// phpinfo() para informações detalhadas
echo "<h2>Informações Detalhadas do PHP</h2>";
phpinfo();
?>
