<?php

namespace App\Controllers;

use App\Models\Product;
use App\Models\Stock;

class ProductController
{
    private $productModel;
    private $stockModel;

    public function __construct()
    {
        $this->productModel = new Product();
        $this->stockModel = new Stock();
    }

    public function index()
    {
        $products = $this->productModel->all();
        require __DIR__ . '/../Views/product/index.php';
    }

    public function create()
    {
        require __DIR__ . '/../Views/product/form.php';
    }

    public function store()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $name = $_POST['name'] ?? '';
            $variations = $_POST['variations'] ?? [];

            if (empty($name)) {
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_PRODUCT_NAME_IS_REQUIRED];
                header('Location: /products/create');
                exit();
            }

            $product_id = $this->productModel->create($name);

            if ($product_id) {
                foreach ($variations as $variation) {
                    if (!empty($variation['name']) && $variation['quantity'] >= 0 && isset($variation['price']) && $variation['price'] >= 0) {
                        $this->stockModel->create($product_id, $variation['name'], $variation['quantity'], $variation['price']);
                    } else {
                        error_log(MSG_INVALID_VARIATION_TO_CREATE . json_encode($variation));
                    }
                }
                $_SESSION['message'] = ['type' => 'success', 'text' => MSG_PRODUCT_AND_VARIATION_CREATED_SUCESSFULLY];
                header('Location: /products');
                exit();
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_PRODUCT_CREATED_ERROR];
                header('Location: /products/create');
                exit();
            }
        }
    }

    public function edit()
    {
        $id = $_GET['id'] ?? null;
        if (!$id) {
            $_SESSION['message'] = ['type' => 'error', 'text' => MSG_PRODUCT_ID_NOT_SPECIFIED_FOR . 'edição'];
            header('Location: /products');
            exit();
        }

        $product = $this->productModel->find($id);
        if (!$product) {
            $_SESSION['message'] = ['type' => 'error', 'text' => MSG_PRODUCT_NOT_FOUND];
            header('Location: /products');
            exit();
        }

        require __DIR__ . '/../Views/product/form.php';
    }

    public function update()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;
            $name = $_POST['name'] ?? '';
            $variations = $_POST['variations'] ?? [];
            $deleted_variations = $_POST['deleted_variations'] ?? '';

            if (!$id || empty($name)) {
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_PRODUCT_DATA_INVALID_TO_UPDATE];
                header('Location: /products/edit?id=' . $id);
                exit();
            }

            $product_updated = $this->productModel->update($id, $name);

            if (!empty($deleted_variations)) {
                $deleted_ids = explode(',', $deleted_variations);
                foreach ($deleted_ids as $stock_id) {
                    if (is_numeric($stock_id)) {
                        $this->stockModel->delete($stock_id);
                    }
                }
            }

            foreach ($variations as $variation) {
                if (!empty($variation['name']) && $variation['quantity'] >= 0 && isset($variation['price']) && $variation['price'] >= 0) {
                    if (isset($variation['id']) && !empty($variation['id'])) {
                        $this->stockModel->update($variation['id'], $variation['name'], $variation['quantity'], $variation['price']);
                    } else {
                        $this->stockModel->create($id, $variation['name'], $variation['quantity'], $variation['price']);
                    }
                } else {
                    error_log(MSG_INVALID_VARIATION_TO_UPDATE . json_encode($variation));
                }
            }

            if ($product_updated) {
                $_SESSION['message'] = ['type' => 'success', 'text' => MSG_PRODUCT_AND_VARIATION_UPDATED_SUCESSFULLY];
                header('Location: /products');
                exit();
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_PRODUCT_UPDATED_ERROR];
                header('Location: /products/edit?id=' . $id);
                exit();
            }
        }
    }

    public function delete()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $id = $_POST['id'] ?? null;

            if (!$id) {
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_PRODUCT_ID_NOT_SPECIFIED_FOR . ' exclusão.'];
                header('Location: /products');
                exit();
            }

            if ($this->productModel->delete($id)) {
                $_SESSION['message'] = ['type' => 'success', 'text' => MSG_PRODUCT_DELETED_SUCCESSFULLY];
            } else {
                $_SESSION['message'] = ['type' => 'error', 'text' => MSG_PRODUCT_DELETED_ERROR];
            }
            header('Location: /products');
            exit();
        }
    }
}
