<?php
// Este arquivo verifica se o servidor atende aos requisitos mínimos para executar o HelpDesk
echo "<h1>Verificação de Compatibilidade do HelpDesk</h1>";

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
?>
