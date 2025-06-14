<?php
/**
 * Adicionar meta viewport e referência ao CSS móvel em todas as páginas PHP
 * Script de atualização para adequar o sistema ao uso em dispositivos móveis
 */

// Diretório com os arquivos PHP
$directory = dirname(__DIR__) . '/public';

// Lista de arquivos para ignorar
$ignoreFiles = ['api_cors.php', 'client_auth_check.php', 'components.php'];

// Encontra todos os arquivos PHP no diretório
$files = glob($directory . '/*.php');

// Contador de arquivos atualizados
$updatedFiles = 0;

foreach ($files as $file) {
    // Ignora arquivos da lista de exceções
    $filename = basename($file);
    if (in_array($filename, $ignoreFiles)) {
        echo "Ignorando $filename...\n";
        continue;
    }
    
    // Lê o conteúdo do arquivo
    $content = file_get_contents($file);
    
    // Verifica se já tem a meta viewport
    if (strpos($content, 'meta name="viewport"') !== false) {
        echo "$filename já contém meta viewport, pulando...\n";
        continue;
    }
    
    // Adiciona meta viewport
    $content = preg_replace(
        '/<meta charset="UTF-8">/i',
        '<meta charset="UTF-8">' . PHP_EOL . '    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">',
        $content
    );
    
    // Verifica se já tem o link para o CSS móvel
    if (strpos($content, 'mobile.css') !== false) {
        echo "$filename já contém referência ao CSS móvel, pulando...\n";
    } else {
        // Adiciona referência ao CSS móvel após o CSS principal
        $content = preg_replace(
            '/<link rel="stylesheet" href="assets\/style.css">/i',
            '<link rel="stylesheet" href="assets/style.css">' . PHP_EOL . '    <link rel="stylesheet" href="assets/mobile.css">',
            $content
        );
    }
    
    // Salva o arquivo modificado
    file_put_contents($file, $content);
    $updatedFiles++;
    
    echo "Arquivo $filename atualizado com sucesso!\n";
}

echo "Total: $updatedFiles arquivos foram atualizados para suportar dispositivos móveis.\n";
?>
