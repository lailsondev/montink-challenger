# 🧪 Projeto de Teste Técnico - Montink

Este projeto foi desenvolvido como parte do processo seletivo da **Montink**.

## 🚀 Tecnologias Utilizadas

- **PHP** (sem framework)
- **MySQL**
- **JavaScript Vanilla**
- **Bootstrap**

## 📦 Bibliotecas e Dependências

- [`vlucas/phpdotenv`](https://github.com/vlucas/phpdotenv): gerenciamento das variáveis de ambiente via `.env`
- [`PHPMailer`](https://github.com/PHPMailer/PHPMailer): envio de e-mails

## 🗃️ Banco de Dados

O arquivo de estrutura inicial (`DDL`) se encontra em: `database/database.sql`

Importe este arquivo no seu MySQL para criar as tabelas necessárias.

## ⚙️ Execução do Projeto

1. **Clone o repositório:**

```bash
git clone https://github.com/lailsondev/montink-challenger
```

## ⚙️ Instale as Dependencias Via Composer
`composer install`

## ⚙️ Configure o `.env`

<pre>DB_HOST=localhost
DB_NAME=montink
DB_USER=root
DB_PASS=root

SMTP_HOST=sandbox.smtp.mailtrap.io
SMTP_PORT=2525
SMTP_USERNAME=usuario
SMTP_PASSWORD=senha
SMTP_FROM_EMAIL=from@example.com
SMTP_FROM_NAME="Loja Teste Montink"

WEBHOOK_SECRET_TOKEN=base64_secret_token</pre>

## 🚀 Suba o Servidor
`php -S localhost:8000 -t public`

## 🌐 Acesse no navegador
`http://localhost:8000`
