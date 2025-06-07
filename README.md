# HelpDesk PHP

Este projeto é um sistema de HelpDesk simples, desenvolvido em PHP, inspirado no https://helpdesk.ip.tv/open.php. Ele permite o registro, acompanhamento e gerenciamento de chamados de suporte técnico, com upload de arquivos e controle de acesso básico.

> **Atenção:** Este sistema está em fase de testes e está aberto a sugestões de melhorias! Sinta-se à vontade para contribuir ou enviar feedback. 
# Link para testa-lo:
[Clique aqui!](http://20.195.168.106:8000/open.php) 

## Objetivo
Oferecer uma base para sistemas de chamados, facilitando o controle de solicitações de suporte em pequenas empresas ou equipes.

## Como rodar o projeto

1. Instale o [Composer](https://getcomposer.org/) se ainda não tiver.
2. Execute `composer install` na raiz do projeto para instalar as dependências.
3. Configure um servidor local (ex: Apache, XAMPP, Laragon) apontando para a pasta do projeto.
4. Execute: php -S localhost:8000 -t public
5. Acesse `http://localhost/open.php` ou `http://localhost:8000/open.php` no navegador (caso use o servidor embutido do PHP).

## Estrutura do Projeto
- `public/` — arquivos acessíveis publicamente (index.php, open.php, tickets.php, login.php, logout.php, edit_ticket.php, delete_ticket.php, buscarchamados.html, chat.php, assets)
- `src/` — código-fonte PHP (controllers, models, views)
  - `Controller/` — lógica de controle (ex: TicketController.php)
  - `Model/` — classes de dados (ex: Ticket.php)
  - `View/` — templates de interface (ex: open_form.php, success.php, buscarchamados.php)
- `uploads/` — arquivos enviados pelos usuários (imagens anexadas aos chamados)
- `vendor/` — dependências gerenciadas pelo Composer
- `tickets.txt` — base de dados dos chamados (JSON)
- `chat_{id}.txt` — histórico de mensagens do chat de cada chamado

## Funcionalidades
- **Abertura de Chamados:** Formulário para registrar solicitações de suporte.
- **Listagem de Chamados:** Visualização de todos os tickets cadastrados.
- **Edição e Exclusão:** Permite editar ou remover chamados existentes.
- **Login/Logout:** Controle de acesso para áreas restritas (admin/técnico).
- **Upload de Arquivos:** Anexação de imagens aos chamados.
- **Chat Cliente-Técnico:** Chat em tempo real vinculado a cada chamado, permitindo comunicação entre cliente e técnico.
- **Busca de Chamados:** Consulta de chamados por e-mail ou telefone.
- **Envio de E-mail:** (Opcional, se configurado) Notificação por e-mail ao abrir chamado.

## Novidades e Melhorias Recentes
- **Chat integrado por chamado:** Usuários e técnicos podem conversar em tempo real em cada chamado.
- **Página buscarchamados.html:** Permite ao usuário buscar seus chamados e acessar o chat diretamente.
- **Botão de Chat em tickets.php:** Técnicos e admins podem acessar o chat do chamado diretamente pela lista de tickets.
- **Controle de permissão no chat:** Apenas técnicos logados podem responder como técnico, mas qualquer usuário pode enviar mensagens.

## Fluxo de Uso
1. O usuário acessa `open.php` e preenche o formulário para abrir um chamado.
2. O chamado é salvo e pode ser visualizado em `tickets.php` (admin/técnico) ou buscado em `buscarchamados.html` (usuário).
3. Usuários autenticados podem editar ou excluir chamados.
4. É possível anexar imagens, que ficam salvas em `uploads/`.
5. O login é feito via `login.php` e o logout via `logout.php`.
6. O chat pode ser acessado pelo usuário em `buscarchamados.html` ou pelo técnico/admin em `tickets.php`.

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

## Principais Classes, Funções e Atributos
### Model: `Ticket.php`
- **Atributos:**
  - `id`, `titulo`, `descricao`, `status`, `data_criacao`, `anexo`
- **Funções:**
  - `criar()`, `listar()`, `buscarPorId($id)`, `atualizar($id)`, `excluir($id)`

### Controller: `TicketController.php`
- **Funções:**
  - `open()`, `edit()`, `delete()`, `list()`, `search()`

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
## Desenvolvido por Gabriel Arezi.
Sinta-se à vontade para sugerir melhorias ou reportar problemas!
