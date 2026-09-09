<?php
session_start();

if (isset($_GET['clear'])) {
    $_SESSION['cart'] = [];
} elseif (isset($_GET['key'])) {
    unset($_SESSION['cart'][$_GET['key']]);
}

include 'get_cart_html.php';
?>