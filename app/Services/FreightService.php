<?php

namespace App\Services;

class FreightService
{
    public function calculateFreight($subtotal)
    {
        if ($subtotal >= 52.00 && $subtotal <= 166.59) {
            return 15.00;
        } elseif ($subtotal > 200.00) {
            return 0.00;
        } else {
            return 20.00;
        }
    }
}