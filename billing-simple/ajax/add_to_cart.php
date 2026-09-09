<?php
session_start();
include '../config/db.php';

$product_id = intval($_POST['product_id']);
$qty = intval($_POST['qty']);

$result = mysqli_query($conn, "SELECT * FROM products WHERE id=$product_id");
$product = mysqli_fetch_assoc($result);

if ($product) {
    $key = uniqid();
    $_SESSION['cart'][$key] = [
        'product_id' => $product['id'],
        'name' => $product['name'],
        'price' => $product['price'],
        'qty' => $qty,
        'total' => $product['price'] * $qty
    ];
}

include 'get_cart_html.php';
?>