<?php
/**
 * Arquivo de componentes comuns para garantir consistência nas páginas
 * Inclui meta tags e configurações responsivas para dispositivos móveis
 */

/**
 * Função que gera o cabeçalho HTML comum para todas as páginas
 * @param string $title Título da página
 * @param array $extraStyles Estilos CSS adicionais específicos da página
 * @return string HTML com o cabeçalho
 */
function generateHeader($title, $extraStyles = []) {
    $header = '<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0, maximum-scale=1.0, user-scalable=no">
    <title>' . htmlspecialchars($title) . ' - HelpDesk</title>
    <link rel="stylesheet" href="assets/style.css">
    <link rel="stylesheet" href="assets/mobile.css">';
    
    // Adiciona estilos extras se fornecidos
    if (!empty($extraStyles)) {
        foreach ($extraStyles as $style) {
            $header .= "\n    " . $style;
        }
    }
    
    $header .= '
</head>
<body>';
    
    return $header;
}

/**
 * Função que gera o rodapé HTML comum para todas as páginas
 * @param bool $includeNightModeSwitch Se deve incluir o botão de alternância de modo noturno
 * @return string HTML com o rodapé
 */
function generateFooter($includeNightModeSwitch = true) {
    $footer = '';
    
    // Adiciona o botão de alternância de modo noturno se solicitado
    if ($includeNightModeSwitch) {
        $footer .= '
    <div class="mode-switch light">
        <input type="checkbox" id="night-mode">
        <label for="night-mode">Modo Noturno</label>
    </div>';
    }
    
    // Script para alternância de modo noturno
    $footer .= '
    <script>
        // Verificar preferência salva
        document.addEventListener("DOMContentLoaded", function() {
            const body = document.body;
            const modeSwitch = document.querySelector(".mode-switch");
            const nightModeCheckbox = document.getElementById("night-mode");
            
            // Recuperar modo do localStorage
            const isDarkMode = localStorage.getItem("darkMode") === "true";
            
            // Aplicar modo ao carregar a página
            if (isDarkMode) {
                body.classList.add("night");
                if (modeSwitch) modeSwitch.classList.remove("light");
                if (nightModeCheckbox) nightModeCheckbox.checked = true;
            }
            
            // Listener para o checkbox de modo noturno
            if (nightModeCheckbox) {
                nightModeCheckbox.addEventListener("change", function() {
                    if (this.checked) {
                        body.classList.add("night");
                        if (modeSwitch) modeSwitch.classList.remove("light");
                        localStorage.setItem("darkMode", "true");
                    } else {
                        body.classList.remove("night");
                        if (modeSwitch) modeSwitch.classList.add("light");
                        localStorage.setItem("darkMode", "false");
                    }
                });
            }
        });
    </script>
</body>
</html>';
    
    return $footer;
}
?>
