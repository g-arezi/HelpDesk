# HelpDesk PHP

Este projeto é um sistema de HelpDesk simples, desenvolvido em PHP, inspirado em [helpdesk.ip.tv/open.php](https://helpdesk.ip.tv/open.php). Permite o registro, acompanhamento e gerenciamento de chamados de suporte técnico, com upload de arquivos e controle de acesso básico.

> **Atenção:** Este sistema está em fase de testes e aberto a sugestões de melhorias! Sinta-se à vontade para contribuir ou enviar feedback.

---

## Objetivo

Oferecer uma base para sistemas de chamados, facilitando o controle de solicitações de suporte em pequenas empresas ou equipes.

---

## Como Rodar o Projeto

1. Instale o [Composer](https://getcomposer.org/) se ainda não tiver.
2. Execute `composer install` na raiz do projeto para instalar as dependências.
3. Configure um servidor local (ex: Apache, XAMPP, Laragon) apontando para a pasta do projeto.
4. Execute: `php -S localhost:8000 -t public`
5. Acesse `http://localhost/open.php` ou `http://localhost:8000/open.php` no navegador (caso use o servidor embutido do PHP).

---

## Estrutura do Projeto

- `public/` — arquivos acessíveis publicamente (`index.php`, `open.php`, `tickets.php`, `login.php`, `logout.php`, `edit_ticket.php`, `delete_ticket.php`, `buscarchamados.html`, `chat.php`, `assets/`)
- `src/` — código-fonte PHP
  - `Controller/` — lógica de controle (ex: `TicketController.php`)
  - `Model/` — classes de dados (ex: `Ticket.php`)
  - `View/` — templates de interface (ex: `open_form.php`, `success.php`, `buscarchamados.php`)
- `uploads/` — arquivos enviados pelos usuários (imagens anexadas aos chamados)
- `vendor/` — dependências gerenciadas pelo Composer
- `tickets.txt` — base de dados dos chamados (JSON)
- `chat_{id}.txt` — histórico de mensagens do chat de cada chamado

---

## Funcionalidades

- **Abertura de Chamados:** Formulário para registrar solicitações de suporte.
- **Listagem de Chamados:** Visualização de todos os tickets cadastrados.
- **Edição e Exclusão:** Permite editar ou remover chamados existentes.
- **Login/Logout:** Controle de acesso para áreas restritas (admin/técnico).
- **Upload de Arquivos:** Anexação de imagens aos chamados.
- **Chat Cliente-Técnico:** Chat em tempo real vinculado a cada chamado, permitindo comunicação entre cliente e técnico.
- **Busca de Chamados:** Consulta de chamados por e-mail ou telefone.
- **Envio de E-mail:** (Opcional, se configurado) Notificação por e-mail ao abrir chamado.

---

## Novidades e Melhorias Recentes

- **Chat integrado por chamado:** Usuários e técnicos podem conversar em tempo real em cada chamado.
- **Página buscarchamados.html:** Permite ao usuário buscar seus chamados e acessar o chat diretamente.
- **Botão de Chat em tickets.php:** Técnicos e admins podem acessar o chat do chamado diretamente pela lista de tickets.
- **Controle de permissão no chat:** Apenas técnicos logados podem responder como técnico, mas qualquer usuário pode enviar mensagens.

---

## Como Usar em um Servidor Apache ou Hospedagem

### 1. Subindo para um Servidor Apache Local (XAMPP, WAMP, Laragon, etc.)

1. Clone ou envie os arquivos do projeto para a pasta `htdocs` (XAMPP) ou `www` (WAMP/Laragon) do seu servidor local.
2. Instale as dependências:  
   No terminal, na raiz do projeto, execute: `composer install`
3. Ajuste as permissões das pastas `uploads/` e da raiz do projeto para garantir que o PHP possa gravar arquivos.
4. Acesse pelo navegador:  
   - `http://localhost/HelpDesk/public/open.php`  
   - Ou, se estiver em uma subpasta: `http://localhost/sua-pasta/public/open.php`

### 2. Subindo para um Site de Hospedagem Compartilhada

1. Envie todos os arquivos do projeto (exceto a pasta `.git` e arquivos de desenvolvimento) para o diretório público do seu site, geralmente chamado `public_html`, `www` ou `htdocs`.
2. Instale as dependências:  
   - Se sua hospedagem permite SSH, acesse via terminal e rode:  
     ```
     composer install
     ```
   - Se não permite, rode `composer install` localmente e envie também a pasta `vendor/` para o servidor.
3. Ajuste as permissões das pastas `uploads/` e da raiz do projeto para garantir que o PHP possa gravar arquivos.
4. Configure o diretório público:  
   - Se possível, aponte o domínio/subdomínio para a pasta `public/` do projeto.
   - Se não for possível, mova o conteúdo da pasta `public/` para a raiz do diretório público e ajuste os caminhos dos includes no código, se necessário.
5. Acesse pelo navegador:  
   - `https://seudominio.com/open.php`  
   - Ou `https://seudominio.com/public/open.php`

### 3. Observações Importantes

- **Banco de dados:** Este sistema usa arquivos `.txt` para armazenar chamados e mensagens de chat. Não é necessário configurar banco de dados.
- **Permissões:** Certifique-se de que o PHP tem permissão de escrita nas pastas onde serão salvos os arquivos (`uploads/`, `tickets.txt`, `chat_{id}.txt`).
- **Segurança:** Para ambientes de produção, recomenda-se proteger as pastas de dados e considerar migração para banco de dados.
- **URL amigável:** Se quiser URLs mais limpas, configure um `.htaccess` para redirecionar requisições para a pasta `public/`.

---

## Fluxo de Uso

1. O usuário acessa `open.php` e preenche o formulário para abrir um chamado.
2. O chamado é salvo e pode ser visualizado em `tickets.php` (admin/técnico) ou buscado em `buscarchamados.html` (usuário).
3. Usuários autenticados podem editar ou excluir chamados.
4. É possível anexar imagens, que ficam salvas em `uploads/`.
5. O login é feito via `login.php` e o logout via `logout.php`.
6. O chat pode ser acessado pelo usuário em `buscarchamados.html` ou pelo técnico/admin em `tickets.php`.

---

## Estrutura Lógica do Projeto

### Cadastro e Gerenciamento de Chamados

- Formulário em `open_form.php` (ou `open.php`) para abertura de chamados.
- Dados são salvos em `tickets.txt`.
- Listagem e gerenciamento em `tickets.php` (restrito a admin/técnico).

### Busca de Chamados

- Usuário busca chamados por e-mail ou telefone em `buscarchamados.html`.
- Backend em `buscarchamados.php` retorna os chamados filtrados.

### Chat Cliente-Técnico

- Mensagens do chat são salvas em arquivos `chat_{id}.txt`.
- Endpoint `chat.php` gerencia envio e leitura das mensagens.
- Frontend do chat integrado em `buscarchamados.html` e acessível por técnicos/admins via botão em `tickets.php`.
- Permissões: qualquer usuário pode enviar mensagem, mas apenas técnicos logados são identificados como tal.

### Autenticação

- Login e logout em `login.php` e `logout.php`.
- Controle de sessão para restringir acesso a áreas administrativas.

### Upload de Arquivos

- Imagens anexadas são salvas em `uploads/`.
- Suporte a colar/arrastar imagens no formulário de abertura de chamado.

---

## Principais Classes, Funções e Atributos

### Model: `Ticket.php`

- **Atributos:**  
  `id`, `titulo`, `descricao`, `status`, `data_criacao`, `anexo`
- **Funções:**  
  `criar()`, `listar()`, `buscarPorId($id)`, `atualizar($id)`, `excluir($id)`

### Controller: `TicketController.php`

- **Funções:**  
  `open()`, `edit()`, `delete()`, `list()`, `search()`

### View

- **open_form.php:** Formulário de abertura e busca de chamados.
- **success.php:** Tela de sucesso após abrir chamado.
- **buscarchamados.html:** Busca e chat de chamados para o usuário.
- **tickets.php:** Listagem e gerenciamento de chamados para admin/técnico.

### Chat

- **chat.php:** Endpoint para envio e leitura de mensagens do chat.
- **buscarchamados.html:** Interface do chat para usuário.
- **Botão Chat em tickets.php:** Acesso rápido ao chat do chamado para técnico/admin.

---

## Exemplos de Uso

### 1. Abrir um Chamado

- Acesse `/open.php`
- Preencha telefone, e-mail, selecione o tópico e descreva o problema.
- Clique em "Abrir chamado".

### 2. Buscar Chamados

- Acesse `/buscarchamados.html`
- Informe seu e-mail ou telefone.
- Veja a lista de chamados e clique em "Abrir Chat" para conversar com o técnico.

### 3. Chat do Chamado

- Técnicos/admins: acesse `/tickets.php`, clique em "Chat" ao lado do chamado.
- Usuários: acesse `/buscarchamados.html?email=seu@email.com` e clique em "Abrir Chat".
- Envie mensagens e acompanhe as respostas em tempo real.

### 4. Login Técnico/Admin

- Acesse `/login.php`
- Usuário técnico: `tecnico` | Senha: `tecnico321`
- Usuário admin: `admin` | Senha: `admin321`

---

## Observações

- Os dados dos chamados e chats são salvos em arquivos `.txt` para facilitar testes e manutenção.
- O sistema pode ser facilmente adaptado para uso com banco de dados.
- O chat é simples, mas pode ser expandido para notificações, anexos e mais recursos.

---

## HelpDesk - Guia de Publicação Completo

Este guia detalha como publicar o sistema HelpDesk em diferentes ambientes, incluindo VPS Windows, VPS Linux, servidores Apache, hospedagem compartilhada e outros métodos. Siga o passo a passo conforme seu cenário.

---

## 1. Publicando em VPS Windows

### 1.1. Pré-requisitos
- PHP instalado (https://windows.php.net/download/)
- Node.js e npm instalados (https://nodejs.org/)
- (Opcional) IIS, XAMPP ou WAMP para servir arquivos PHP

### 1.2. Backend (PHP)
1. Extraia o projeto em uma pasta, ex: `E:\Helpdesk\HelpDesk`.
2. Instale as dependências PHP:
   ```powershell
   cd E:\Helpdesk\HelpDesk
   composer install
   ```
3. Inicie o backend com o servidor embutido do PHP:
   ```powershell
   cd E:\Helpdesk\HelpDesk\public
   php -S 0.0.0.0:8000
   ```
4. Libere a porta 8000 no firewall:
   ```powershell
   New-NetFirewallRule -DisplayName "PHP Backend 8000" -Direction Inbound -LocalPort 8000 -Protocol TCP -Action Allow
   ```

---

## 2. Publicando em VPS Linux (Ubuntu/Debian)

### 2.1. Pré-requisitos
- PHP, Composer, Node.js, npm
- (Opcional) Apache ou Nginx

### 2.2. Backend (PHP)
1. Instale dependências:
   ```bash
   sudo apt update
   sudo apt install php php-cli php-mbstring unzip curl composer
   ```
2. Extraia o projeto, ex: `/home/usuario/HelpDesk`
3. Instale dependências PHP:
   ```bash
   cd /home/usuario/HelpDesk
   composer install
   ```
4. Inicie o backend (modo rápido):
   ```bash
   cd /home/usuario/HelpDesk/public
   php -S 0.0.0.0:8000
   ```
5. Libere a porta 8000:
   ```bash
   sudo ufw allow 8000
   ```

---

## 3. Publicando com Apache (Windows ou Linux)

### 3.1. Backend (PHP)
1. Copie o conteúdo da pasta `public` para o diretório do site no Apache (ex: `C:/xampp/htdocs/helpdesk` ou `/var/www/html/helpdesk`).
2. Configure o Apache para servir o diretório.
3. Certifique-se de que o Apache está rodando e a porta 80 está liberada.
4. Acesse pelo navegador: `http://localhost/helpdesk/open.php` ou `http://SEU_IP/helpdesk/open.php`

---

## 4. Publicando em Hospedagem Compartilhada (cPanel, HostGator, etc.)

### 4.1. Backend (PHP)
1. Faça upload dos arquivos da pasta `public` para a pasta `public_html` ou similar.
2. Faça upload da pasta `logs` e garanta permissão de escrita.
3. Se necessário, ajuste caminhos em arquivos PHP para refletir a estrutura da hospedagem.

---

## 5. Publicando com Nginx (Linux)

### 5.1. Backend (PHP)
- Configure um bloco de servidor para servir a pasta `public` e encaminhar requisições PHP para o PHP-FPM.
- Sempre ajuste as permissões das pastas:
   ```bash
   sudo chown -R www-data:www-data /caminho/para/seu/projeto
   sudo chmod -R 755 /caminho/para/seu/projeto
   ```
- Exemplo : `chmod -R 775 uploads logs` e `chmod 664 logs/tickets.txt`

### 5.2. Frontend (React)
- Sirva a pasta `build` como arquivos estáticos.
- Para React Router, adicione:
   ```nginx
   location / {
       try_files $uri /index.html;
   }
   ```
- Para proxy de API, adicione:
   ```nginx
   location /api/ {
       proxy_pass http://localhost:8000/;
       proxy_set_header Host $host;
   }
   ```

---

## 6. Dicas Gerais
- Sempre ajuste o `.env` do frontend para apontar para o backend correto.
- Libere as portas necessárias no firewall.
- Para produção, prefira servir o backend com Apache/Nginx e PHP-FPM.
- Garanta permissão de escrita na pasta `logs`.
- Para HTTPS, configure certificados SSL no servidor web.

---

## 7. Suporte
Em caso de dúvidas, consulte a documentação oficial do PHP, React, Apache, Nginx ou entre em contato com o suporte da sua hospedagem.

---

## Desenvolvido por Gabriel Arezi

- Meu portfólio para contato: [Clique Aqui](https://portifolio-beta-five-52.vercel.app/)
- Sinta-se à vontade para sugerir melhorias ou reportar problemas!
