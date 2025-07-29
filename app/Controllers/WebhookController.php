<?php

namespace App\Controllers;

use App\Models\Order;
use App\Models\Stock;
use App\Services\EmailService;

class WebhookController
{
    private $orderModel;
    private $stockModel;
    private $emailService;

    public function __construct()
    {
        $this->orderModel = new Order();
        $this->stockModel = new Stock();
        $this->emailService = new EmailService();
    }

    public function handle()
    {
        $headers = getallheaders();
        $secretToken = $_ENV['WEBHOOK_SECRET_TOKEN'];

        if (!isset($headers['X-Webhook-Token']) || $headers['X-Webhook-Token'] !== $secretToken) {
            http_response_code(403);
            echo json_encode(['status' => 'error', 'message' => MSG_INVALID_TOKEN]);
            exit();
        }

        $input = file_get_contents('php://input');
        $data = json_decode($input, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => MSG_INVALID_JSON]);
            exit();
        }

        $orderId = $data['order_id'] ?? null;
        $status = $data['status'] ?? null;

        if (!$orderId || !$status) {
            http_response_code(400);
            echo json_encode(['status' => 'error', 'message' => MSG_FIELDS_ORDER_ID_AND_STATUS_IS_REQUIRED]);
            exit();
        }

        $order = $this->orderModel->find($orderId);
        if (!$order) {
            http_response_code(404);
            echo json_encode(['status' => 'error', 'message' => MSG_ORDER_NOT_FOUND]);
            exit();
        }

        if ($status === 'cancelado') {
            $this->orderModel->getConnection()->beginTransaction();
            try {
                $orderItems = $this->orderModel->getOrderItems($orderId);
                foreach ($orderItems as $item) {
                    $this->stockModel->increaseStock($item['variacao_id'], $item['quantidade']);
                }

                $this->orderModel->delete($orderId);
                $this->orderModel->getConnection()->commit();

                http_response_code(200);
                echo json_encode(['status' => 'success', 'message' => MSG_ORDER_CANCELD_SUCCESSFULLY]);
            } catch (\Exception $e) {
                $this->orderModel->getConnection()->rollBack();
                http_response_code(500);
                echo json_encode(['status' => 'error', 'message' => MSG_ORDER_CANCELD_ERROR . $e->getMessage()]);
            }
            exit;
        }

        if ($this->orderModel->updateStatus($orderId, $status)) {
            $orderCustomerEmail = $order['email_cliente'];
            $orderItemsForEmail = $this->orderModel->getOrderItems($orderId);
            
            $this->emailService->sendOrderConfirmationEmail($orderCustomerEmail, $order, $orderItemsForEmail, $status);
            http_response_code(200);
            echo json_encode(['status' => 'success', 'message' => MSG_ORDER_UPDATED_SUCCESSFULLY . $status]);
        } else {
            http_response_code(500);
            echo json_encode(['status' => 'error', 'message' => MSG_ORDER_UPDATED_ERROR]);
        }

        exit;
    }
}