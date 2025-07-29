<?php

namespace App\Controllers;

use App\Models\Coupon;

class CouponController
{
    private $couponModel;

    public function __construct()
    {
        $this->couponModel = new Coupon();
    }

    public function index()
    {
        $coupons = $this->couponModel->all();
        require __DIR__ . '/../Views/coupons/index.php';
    }

    public function create()
    {
        require __DIR__ . '/../Views/coupons/form.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $code = $_POST['code'] ?? '';
            $type = $_POST['type'] ?? '';
            $value = $_POST['value'] ?? 0;
            $valid_from = $_POST['valid_from'] ?? '';
            $valid_to = $_POST['valid_to'] ?? '';
            $min_value = $_POST['min_value'] ?? null;

            if (empty($code) || empty($type) || $value <= 0 || empty($valid_from) || empty($valid_to)) {
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_ALL_FIELDS_COUPON_REQUIRED];
                header('Location: /coupons/create');
                exit();
            }

            if ($this->couponModel->create($code, $type, $value, $valid_from, $valid_to, $min_value)) {
                $_SESSION['message'] = ['type' => 'success', 'text' => MSG_COUPON_CREATED_SUCCESS];
                header('Location: /coupons');
                exit();
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_COUPON_CREATED_ERROR];
                header('Location: /coupons/create');
                exit();
            }
        }
    }
}