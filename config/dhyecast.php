<?php

/*
|--------------------------------------------------------------------------
| Konfigurasi bisnis Dhyecast
|--------------------------------------------------------------------------
| Satu sumber kebenaran untuk opsi pengiriman, metode pembayaran, dan
| status order. Nilai ini menggantikan konstanta SHIPPING_OPTIONS /
| PAYMENT_LABELS yang sebelumnya hidup di orderService.js (React).
*/

return [

    'shipping' => [
        'standard' => ['label' => 'Standard Delivery', 'title' => 'Standard Shipping (3-5 Days)', 'cost' => 25000],
        'express' => ['label' => 'Express Delivery', 'title' => 'Express Shipping (1-2 Days)', 'cost' => 50000],
    ],

    'payments' => [
        'bank_transfer' => 'Bank Transfer',
        'qris' => 'QRIS',
    ],

    'order_statuses' => ['pending', 'paid', 'shipped', 'completed', 'cancelled'],

    // Origin React (Vite) yang diizinkan memanggil API
    'frontend_url' => env('FRONTEND_URL', 'http://localhost:5173'),

    'low_stock_threshold' => 5,

];
