# Configuração para Hospedagem

Este arquivo contém informações sobre variáveis de ambiente e configurações que podem precisar ser ajustadas ao hospedar o HelpDesk em um ambiente de produção.

## Configurações do PHP

Verifique se o PHP está configurado corretamente no servidor:

```
file_uploads = On
upload_max_filesize = 10M (ou mais)
post_max_size = 10M (ou mais)
max_execution_time = 60 (ou mais)
memory_limit = 128M (ou mais)
```

## Diretórios e Permissões

Os seguintes diretórios precisam existir e ter permissões de escrita (755 ou 775):

- `logs/`
- `public/uploads/`

Os seguintes arquivos de log devem existir e ter permissões de escrita (644 ou 664):

- `logs/tickets.txt`
- `logs/user_registrations.txt`
- `logs/password_reset_tokens.txt`
- `logs/quick_users.txt`
- `logs/chat_1.txt`

Se os arquivos não existirem, crie-os com o conteúdo inicial `[]` (um array JSON vazio).

## Configuração do Servidor Web

### Apache

Um arquivo `.htaccess` já está incluído no projeto. Se houver problemas, verifique se o módulo `mod_rewrite` está habilitado no Apache.

### Nginx

Se estiver usando Nginx, você precisará configurar manualmente as regras de reescrita e proteção. Aqui está um exemplo de configuração:

```nginx
server {
    listen 80;
    server_name seudominio.com;
    root /caminho/para/helpdesk/public;

    index index.php index.html;

    # Proteger diretórios sensíveis
    location ~ ^/(logs|src|vendor|scripts) {
        deny all;
        return 404;
    }

    # Negar acesso a arquivos ocultos
    location ~ /\. {
        deny all;
        return 404;
    }

    # Processar arquivos PHP
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php7.4-fpm.sock; # Ajuste conforme sua versão do PHP
        fastcgi_index index.php;
        fastcgi_param SCRIPT_FILENAME $document_root$fastcgi_script_name;
        include fastcgi_params;
    }

    # Servir arquivos estáticos
    location ~* \.(jpg|jpeg|gif|png|css|js|ico|xml)$ {
        expires 30d;
        add_header Cache-Control "public, no-transform";
    }

    # Redirecionamento para a pasta public
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

## Roteamento e URLs

Por padrão, as URLs são configuradas para funcionarem em qualquer ambiente. Se você tiver problemas com URLs:

1. Verifique as chamadas a `$_SERVER['HTTP_HOST']` em todo o código
2. Ajuste o protocolo (http ou https) conforme necessário
3. Verifique se o `.htaccess` está configurado corretamente

## Migração de Configurações Locais para Produção

1. Após fazer upload dos arquivos para o servidor, teste todas as funcionalidades
2. Comece verificando se o login e registro funcionam corretamente
3. Teste o upload de arquivos e o sistema de chat
4. Verifique se os emails e notificações estão funcionando (se implementados)

## Segurança Adicional

Considere implementar as seguintes medidas de segurança:

1. Ativar HTTPS (SSL/TLS)
2. Configurar autenticação de dois fatores (2FA)
3. Implementar limitação de taxa (rate limiting) para tentativas de login
4. Configurar backups automáticos para os arquivos de dados

## Monitoramento e Manutenção

1. Verifique regularmente os logs do servidor
2. Faça backup dos arquivos em `logs/` periodicamente
3. Monitore o espaço em disco, especialmente na pasta `public/uploads/`
4. Mantenha o PHP e as dependências atualizadas
