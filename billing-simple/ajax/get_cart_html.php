<?php
session_start();
if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

$html = '<table class="table table-bordered table-sm table-cart"><thead><tr><th>#</th><th>Item</th><th>Price</th><th>Qty</th><th>Total</th><th class="no-print">Action</th></tr></thead><tbody>';

if (count($_SESSION['cart']) > 0) {
    $sl = 1;
    foreach ($_SESSION['cart'] as $key => $item) {
        $html .= "<tr>
            <td>{$sl}</td>
            <td>{$item['name']}</td>
            <td>Rs. {$item['price']}</td>
            <td>{$item['qty']}</td>
            <td>Rs. {$item['total']}</td>
            <td class='no-print'><button class='btn btn-danger btn-sm' onclick='removeFromCart(\"{$key}\")'><i class='fas fa-times'></i></button></td>
        </tr>";
        $sl++;
    }
} else {
    $html .= "<tr><td colspan='6' class='text-center text-muted'>Cart is empty</td></tr>";
}

$html .= '</tbody></table>';
echo $html;
?>