<?php
session_start();
$total = 0;
foreach ($_SESSION['cart'] as $item) {
    $total += $item['total'];
}
header('Content-Type: application/json');
echo json_encode(['total' => $total]);
?>