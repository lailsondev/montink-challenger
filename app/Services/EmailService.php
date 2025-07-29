<?php

namespace App\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $mailer;

    public function __construct()
    {
        $this->mailer = new PHPMailer(true);
        $this->configureMailer();
    }

    private function configureMailer()
    {
        try {
            $this->mailer->isSMTP();
            $this->mailer->Host       = $_ENV['SMTP_HOST'];
            $this->mailer->SMTPAuth   = true;
            $this->mailer->Username   = $_ENV['SMTP_USERNAME'];
            $this->mailer->Password   = $_ENV['SMTP_PASSWORD'];
            $this->mailer->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
            $this->mailer->Port       = $_ENV['SMTP_PORT'];

            $this->mailer->setFrom($_ENV['SMTP_FROM_EMAIL'], $_ENV['SMTP_FROM_NAME']);
            $this->mailer->isHTML(true);
            $this->mailer->CharSet = 'UTF-8';

        } catch (Exception $e) {
            error_log("Erro ao configurar o PHPMailer: " . $e->getMessage());
        }
    }

    public function sendOrderConfirmationEmail($recipientEmail, $orderData, $orderItems)
    {
        try {
            $this->mailer->addAddress($recipientEmail);
            $this->mailer->Subject = 'Confirmação do seu Pedido #' . $orderData['id'];

            $body = "<h1>Obrigado por seu pedido!</h1>";
            $body .= "<p>Seu pedido #<strong>" . $orderData['id'] . "</strong> foi recebido e está sendo processado.</p>";
            $body .= "<h2>Detalhes do Pedido:</h2>";
            $body .= "<p><strong>Subtotal:</strong> R$" . number_format($orderData['subtotal'], 2, ',', '.') . "</p>";
            $body .= "<p><strong>Frete:</strong> R$" . number_format($orderData['frete'], 2, ',', '.') . "</p>";
            if (!empty($orderData['cupom_id'])) {
                $body .= "<p><strong>Desconto:</strong> Cupom Aplicado</p>";
            }
            $body .= "<p><strong>Total:</strong> R$" . number_format($orderData['total'], 2, ',', '.') . "</p>";
            $body .= "<h3>Endereço de Entrega:</h3>";
            $body .= "<p>" . $orderData['endereco'] . ", " . $orderData['numero'] . ($orderData['complemento'] ? " - " . $orderData['complemento'] : "") . "</p>";
            $body .= "<p>" . $orderData['bairro'] . ", " . $orderData['cidade'] . " - " . $orderData['estado'] . "</p>";
            $body .= "<p>CEP: " . $orderData['cep'] . "</p>";

            $body .= "<h3>Itens do Pedido:</h3>";
            $body .= "<table border='1' cellpadding='5' cellspacing='0' style='width:100%; border-collapse: collapse;'>";
            $body .= "<thead><tr><th>Produto</th><th>Variação</th><th>Quantidade</th><th>Preço Unit.</th><th>Subtotal</th></tr></thead>";
            $body .= "<tbody>";
            foreach ($orderItems as $item) {
                $body .= "<tr>";
                $body .= "<td>" . htmlspecialchars($item['produto_nome']) . "</td>";
                $body .= "<td>" . htmlspecialchars($item['variacao_nome'] ?? 'N/A') . "</td>";
                $body .= "<td>" . htmlspecialchars($item['quantidade']) . "</td>";
                $body .= "<td>R$" . number_format($item['preco_unitario'], 2, ',', '.') . "</td>";
                $body .= "<td>R$" . number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') . "</td>";
                $body .= "</tr>";
            }
            $body .= "</tbody></table>";

            $this->mailer->Body = $body;
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail de confirmação: " . $this->mailer->ErrorInfo);
            return false;
        }
    }

    public function sendOrderCancelationEmail($recipientEmail, $orderData, $orderItems)
    {
        try {
            $this->mailer->addAddress($recipientEmail);
            $this->mailer->Subject = 'Confirmação do seu Pedido #' . $orderData['id'];

            $body = "<h1>Obrigado por seu pedido!</h1>";
            $body .= "<p>Seu pedido #<strong>" . $orderData['id'] . "</strong> foi recebido e está sendo processado.</p>";
            $body .= "<h2>Detalhes do Pedido:</h2>";
            $body .= "<p><strong>Subtotal:</strong> R$" . number_format($orderData['subtotal'], 2, ',', '.') . "</p>";
            $body .= "<p><strong>Frete:</strong> R$" . number_format($orderData['frete'], 2, ',', '.') . "</p>";
            if (!empty($orderData['cupom_id'])) {
                $body .= "<p><strong>Desconto:</strong> Cupom Aplicado</p>";
            }
            $body .= "<p><strong>Total:</strong> R$" . number_format($orderData['total'], 2, ',', '.') . "</p>";
            $body .= "<h3>Endereço de Entrega:</h3>";
            $body .= "<p>" . $orderData['endereco'] . ", " . $orderData['numero'] . ($orderData['complemento'] ? " - " . $orderData['complemento'] : "") . "</p>";
            $body .= "<p>" . $orderData['bairro'] . ", " . $orderData['cidade'] . " - " . $orderData['estado'] . "</p>";
            $body .= "<p>CEP: " . $orderData['cep'] . "</p>";

            $body .= "<h3>Itens do Pedido:</h3>";
            $body .= "<table border='1' cellpadding='5' cellspacing='0' style='width:100%; border-collapse: collapse;'>";
            $body .= "<thead><tr><th>Produto</th><th>Variação</th><th>Quantidade</th><th>Preço Unit.</th><th>Subtotal</th></tr></thead>";
            $body .= "<tbody>";
            foreach ($orderItems as $item) {
                $body .= "<tr>";
                $body .= "<td>" . htmlspecialchars($item['produto_nome']) . "</td>";
                $body .= "<td>" . htmlspecialchars($item['variacao_nome'] ?? 'N/A') . "</td>";
                $body .= "<td>" . htmlspecialchars($item['quantidade']) . "</td>";
                $body .= "<td>R$" . number_format($item['preco_unitario'], 2, ',', '.') . "</td>";
                $body .= "<td>R$" . number_format($item['preco_unitario'] * $item['quantidade'], 2, ',', '.') . "</td>";
                $body .= "</tr>";
            }
            $body .= "</tbody></table>";

            $this->mailer->Body = $body;
            $this->mailer->send();
            return true;
        } catch (Exception $e) {
            error_log("Erro ao enviar e-mail de confirmação: " . $this->mailer->ErrorInfo);
            return false;
        }
    }
}