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
- `public/` — arquivos acessíveis publicamente (index.php, open.php, tickets.php, login.php, logout.php, edit_ticket.php, delete_ticket.php, assets)
- `src/` — código-fonte PHP (controllers, models, views)
  - `Controller/` — lógica de controle (ex: TicketController.php)
  - `Model/` — classes de dados (ex: Ticket.php)
  - `View/` — templates de interface (ex: open_form.php, success.php)
- `uploads/` — arquivos enviados pelos usuários (imagens anexadas aos chamados)
- `vendor/` — dependências gerenciadas pelo Composer

## Funcionalidades
- **Abertura de Chamados:** Formulário para registrar solicitações de suporte.
- **Listagem de Chamados:** Visualização de todos os tickets cadastrados.
- **Edição e Exclusão:** Permite editar ou remover chamados existentes.
- **Login/Logout:** Controle de acesso para áreas restritas.
- **Upload de Arquivos:** Anexação de imagens aos chamados.
- **Envio de E-mail:** (Opcional, se configurado) Notificação por e-mail ao abrir chamado.

## Fluxo de Uso
1. O usuário acessa `open.php` e preenche o formulário para abrir um chamado.
2. O chamado é salvo e pode ser visualizado em `tickets.php`.
3. Usuários autenticados podem editar ou excluir chamados.
4. É possível anexar imagens, que ficam salvas em `uploads/`.
5. O login é feito via `login.php` e o logout via `logout.php`.

## Principais Classes, Funções e Atributos
### Model: `Ticket.php`
- **Atributos:**
  - `id`, `titulo`, `descricao`, `status`, `data_criacao`, `anexo`
- **Funções:**
  - `criar()`, `listar()`, `buscarPorId($id)`, `atualizar($id)`, `excluir($id)`

### Controller: `TicketController.php`
- **Funções:**
  - `abrirChamado()`, `editarChamado($id)`, `excluirChamado($id)`, `listarChamados()`

### View
- **open_form.php:** Formulário de abertura de chamado
- **success.php:** Tela de sucesso após operação

## Exemplos de Uso
- Para abrir um chamado, acesse `/open.php` e preencha os campos obrigatórios.
- Para visualizar chamados, acesse `/tickets.php`.
- Para editar/excluir, clique nos botões correspondentes ao lado do chamado (requer login).
- Para anexar uma imagem, utilize o campo de upload no formulário de abertura/edição.

## Personalização e Expansão
- Adicione novos campos ao modelo `Ticket.php` conforme sua necessidade.
- Implemente novos tipos de autenticação ou níveis de acesso.
- Integre com bancos de dados relacionais (MySQL, PostgreSQL) para maior robustez.
- Expanda as views para um layout mais moderno usando frameworks CSS.

## Dependências
- PHP >= 8.0
- Composer
- PHPMailer (envio de e-mails)
- vlucas/phpdotenv (variáveis de ambiente)
- Outras dependências listadas no `composer.json`

## Observações
- Este projeto é apenas uma base. Personalize conforme sua necessidade.
- Para rodar, siga os passos da seção "Como rodar o projeto".
- Os arquivos enviados ficam em `uploads/`.

## Créditos
- Inspirado por https://helpdesk.ip.tv/open.php
- Desenvolvido por Gabriel Arezi.

## Licença
Este projeto está sob a licença MIT. Sinta-se livre para usar, modificar e distribuir.
