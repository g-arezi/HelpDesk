# HelpDesk PHP

Sistema de HelpDesk simples e moderno, desenvolvido em PHP, para registro, acompanhamento e gerenciamento de chamados de suporte técnico. Permite upload de arquivos, chat em tempo real, controle de acesso e busca de chamados. Ideal para pequenas empresas, equipes ou uso pessoal.

> **Atenção:** Sistema em constante evolução! Sugestões e contribuições são bem-vindas.

---

## Novidades e Melhorias Recentes

- **Chat em tempo real por chamado** (cliente e técnico/admin)
- **Busca de chamados por e-mail ou telefone**
- **Botão de chat direto na listagem de tickets**
- **Permissões aprimoradas no chat** (técnico/admin identificado)
- **Upload de imagens com suporte a arrastar/colar**
- **Página de busca amigável para usuários**
- **Controle de sessão e login seguro**
- **Código organizado em MVC (Controller, Model, View)**
- **Pronto para rodar em ambientes Windows e Linux**
- **Compatível com XAMPP, Laragon, Apache, Nginx, hospedagem compartilhada e VPS**

---

## Estrutura do Projeto

- `public/` — arquivos públicos (páginas, assets)
- `src/` — código-fonte PHP (Controller, Model, View)
- `uploads/` — anexos enviados pelos usuários
- `logs/` — arquivos de log e base de dados dos chamados
- `vendor/` — dependências do Composer

---

## Funcionalidades

- **Abertura e acompanhamento de chamados**
- **Chat em tempo real por chamado**
- **Upload de imagens**
- **Busca de chamados por e-mail/telefone**
- **Login/logout para técnicos/admins**
- **Edição e exclusão de chamados**
- **Controle de permissões**

---

## Como Rodar o Projeto

### 1. Requisitos
- PHP 7.4+
- Composer
- (Opcional) Apache, Nginx, XAMPP, Laragon ou hospedagem compatível

### 2. Instalação Rápida (Ambiente Local)

1. Clone ou extraia o projeto em uma pasta de sua preferência.
2. No terminal, acesse a pasta do projeto e execute:
   ```
   composer install
   ```
3. Inicie o servidor embutido do PHP:
   ```
   php -S localhost:8000 -t public
   ```
4. Acesse `http://localhost:8000/open.php` no navegador.

### 3. Ajuste de Permissões (Linux)

Garanta permissão de escrita nas pastas `uploads/` e `logs/`:
```bash
chmod -R 775 uploads logs
chmod 664 logs/tickets.txt
```

No Windows, normalmente não é necessário ajustar permissões.

---

## Tutoriais de Instalação em Diferentes Plataformas

### XAMPP (Windows)
1. Instale o XAMPP e inicie o Apache.
2. Copie o projeto para `C:/xampp/htdocs/HelpDesk`.
3. No terminal, acesse a pasta do projeto e rode:
   ```
   composer install
   ```
4. Acesse `http://localhost/HelpDesk/public/open.php` no navegador.

### Laragon (Windows)
1. Instale o Laragon e inicie o Apache.
2. Copie o projeto para `C:/laragon/www/HelpDesk`.
3. No terminal, acesse a pasta do projeto e rode:
   ```
   composer install
   ```
4. Acesse `http://localhost/HelpDesk/public/open.php`.

### Hospedagem Compartilhada (cPanel, HostGator, etc.)
1. Faça upload dos arquivos para a pasta `public_html`.
2. Envie também as pastas `uploads/` e `logs/`.
3. Se possível, rode `composer install` via SSH. Caso não tenha SSH, rode localmente e envie a pasta `vendor/`.
4. Ajuste permissões das pastas para escrita (via painel ou FTP).
5. Acesse `https://seudominio.com/public/open.php` ou mova o conteúdo de `public/` para a raiz se necessário.

### VPS Linux (Ubuntu/Debian)
1. Instale PHP, Composer e (opcional) Apache/Nginx:
   ```bash
   sudo apt update
   sudo apt install php php-cli composer unzip curl
   ```
2. Extraia o projeto, ex: `/home/usuario/HelpDesk`
3. Rode:
   ```bash
   cd /home/usuario/HelpDesk
   composer install
   ```
4. Inicie o servidor embutido:
   ```bash
   cd public
   php -S 0.0.0.0:8000
   ```
5. Libere a porta 8000:
   ```bash
   sudo ufw allow 8000
   ```
6. Acesse `http://SEU_IP:8000/open.php`

### VPS Windows
1. Instale PHP e Composer.
2. Extraia o projeto, ex: `E:\Helpdesk\HelpDesk`.
3. No PowerShell:
   ```powershell
   cd E:\Helpdesk\HelpDesk
   composer install
   cd public
   php -S 0.0.0.0:8000
   ```
4. Libere a porta 8000 no firewall:
   ```powershell
   New-NetFirewallRule -DisplayName "PHP Backend 8000" -Direction Inbound -LocalPort 8000 -Protocol TCP -Action Allow
   ```
5. Acesse `http://SEU_IP:8000/open.php`

### Apache (Windows ou Linux)
1. Copie o conteúdo da pasta `public` para o diretório do site no Apache (ex: `C:/xampp/htdocs/helpdesk` ou `/var/www/html/helpdesk`).
2. Certifique-se de que o Apache está rodando.
3. Ajuste permissões das pastas `uploads/` e `logs/`.
4. Acesse `http://localhost/helpdesk/open.php`.

### Nginx (Linux)
1. Configure o bloco de servidor para servir a pasta `public` e encaminhar PHP para o PHP-FPM.
2. Ajuste permissões:
   ```bash
   sudo chown -R www-data:www-data /caminho/para/seu/projeto
   sudo chmod -R 775 uploads logs
   sudo chmod 664 logs/tickets.txt
   ```
3. Reinicie o Nginx e acesse pelo navegador.

---

## Como Usar

### Abrir um Chamado
- Acesse `/open.php`, preencha o formulário e envie.

### Buscar Chamados
- Acesse `/buscarchamados.html`, informe e-mail ou telefone e veja seus chamados.

### Chat do Chamado
- Usuário: acesse `/buscarchamados.html` e clique em "Abrir Chat".
- Técnico/Admin: acesse `/tickets.php` e clique em "Chat" ao lado do chamado.

### Login Técnico/Admin
- Acesse `/login.php`
- Técnico: `seu-user` | Senha: `sua-senha` (pode ser alterada no arquivo `src/Model/Usuario.php`)

---

## Exemplos de Uso

### 1. Abrir um Chamado
1. Acesse `http://localhost:8000/open.php` (ou o endereço correspondente ao seu servidor).
2. Preencha os campos obrigatórios: nome, telefone, e-mail, selecione o tópico e descreva o problema.
3. (Opcional) Anexe uma imagem arrastando para o campo ou clicando em "Escolher arquivo".
4. Clique em "Abrir chamado". Você verá uma mensagem de sucesso e receberá o número do seu chamado.

### 2. Buscar Chamados e Acessar o Chat
1. Acesse `http://localhost:8000/buscarchamados.html`.
2. Informe seu e-mail ou telefone cadastrado e clique em "Buscar".
3. Veja a lista de chamados abertos. Clique em "Abrir Chat" para conversar com o técnico responsável.
4. Envie mensagens e acompanhe as respostas em tempo real.

### 3. Gerenciamento para Técnico/Admin
1. Faça login em `http://localhost:8000/login.php` com:
   - Técnico: usuário `seu-user` | senha `sua-senha`
   - Admin: usuário `seu-user` | senha `sua-senha`
2. Acesse `http://localhost:8000/tickets.php` para ver todos os chamados.
3. Edite, exclua ou altere o status dos chamados conforme necessário.
4. Clique em "Chat" ao lado de um chamado para conversar com o usuário.

### 4. Anexar Imagens a um Chamado
- No formulário de abertura, arraste a imagem para o campo de upload ou clique para selecionar um arquivo.
- O arquivo será salvo na pasta `uploads/` e ficará disponível para consulta no chamado.

### 5. Alterar Senha de Técnico/Admin
- As credenciais estão no arquivo `src/Model/Usuario.php`.
- Edite o arquivo para alterar usuário ou senha conforme desejado.

---

## Observações e Dicas
- Os dados são salvos em arquivos `.txt` para facilitar testes e manutenção.
- Para produção, recomenda-se proteger as pastas de dados e considerar migração para banco de dados.
- Sempre garanta permissão de escrita nas pastas `uploads/` e `logs/`.
- Para HTTPS, configure SSL no servidor web.

---

## Suporte
Dúvidas? Consulte a documentação oficial do PHP, Apache, Nginx ou entre em contato com a comunidade.
- [Documentação PHP](https://www.php.net/manual/pt_BR/)
- [Documentação Apache](https://httpd.apache.org/docs/)
- [Documentação Nginx](https://nginx.org/en/docs/)
- [Fórum PHP Brasil](https://forum.php.net/)

---

## Desenvolvido por Gabriel Arezi

- Portfólio: [Clique Aqui](https://portifolio-beta-five-52.vercel.app/)
- Sugestões e melhorias são bem-vindas!
