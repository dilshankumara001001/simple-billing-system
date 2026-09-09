<?php
include 'config/db.php';
$bill_id = intval($_GET['bill_id']);

$bill = mysqli_fetch_assoc(mysqli_query($conn, "SELECT b.*, u.name as cashier, c.name as cust 
    FROM bills b 
    LEFT JOIN customers c ON b.customer_id = c.id 
    JOIN users u ON b.user_id = u.id 
    WHERE b.id=$bill_id"));

$items = mysqli_query($conn, "SELECT bi.*, p.name as pname FROM bill_items bi 
    JOIN products p ON bi.product_id = p.id WHERE bi.bill_id=$bill_id");
?>
<!DOCTYPE html>
<html>
<head>
    <title>View Bill</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print { .no-print { display: none; } }
        .bill-box { max-width: 700px; margin: 30px auto; background: white; padding: 30px; border-radius: 10px; box-shadow: 0 0 20px rgba(0,0,0,0.1); }
    </style>
</head>
<body>
<div class="bill-box" id="print-area">
    <div class="text-center border-bottom pb-3">
        <h3>🛍️ MY SHOP</h3>
        <p>📞 077-1234567 | 📍 Colombo</p>
        <h5><?php echo $bill['bill_no']; ?></h5>
        <small><?php echo $bill['created_at']; ?></small>
    </div>
    <div class="mt-3">
        <p><strong>Cashier:</strong> <?php echo $bill['cashier']; ?></p>
        <p><strong>Customer:</strong> <?php echo $bill['cust'] ?? 'Walk-in'; ?></p>
    </div>
    <table class="table table-bordered">
        <thead><tr><th>Item</th><th>Price</th><th>Qty</th><th>Total</th></tr></thead>
        <tbody>
        <?php while ($item = mysqli_fetch_assoc($items)) { 
            echo "<tr><td>{$item['pname']}</td><td>Rs. {$item['price']}</td><td>{$item['qty']}</td><td>Rs. {$item['total']}</td></tr>";
        } ?>
        </tbody>
        <tfoot>
            <tr><td colspan="3" class="text-end">Total</td><td>Rs. <?php echo $bill['total_amount']; ?></td></tr>
            <tr><td colspan="3" class="text-end">Discount (<?php echo $bill['discount_percent']; ?>%)</td><td>- Rs. <?php echo $bill['discount_amount']; ?></td></tr>
            <tr class="table-success"><td colspan="3" class="text-end fw-bold">Net Amount</td><td>Rs. <?php echo $bill['net_amount']; ?></td></tr>
            <tr><td colspan="3" class="text-end">Paid</td><td>Rs. <?php echo $bill['paid_amount']; ?></td></tr>
            <tr><td colspan="3" class="text-end">Change</td><td>Rs. <?php echo $bill['change_amount']; ?></td></tr>
        </tfoot>
    </table>
    <div class="text-center mt-3"><i>Thank you! Visit Again!</i></div>
</div>
<div class="text-center no-print mt-3">
    <button class="btn btn-warning" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
    <a href="history.php" class="btn btn-secondary">Back</a>
</div>
</body>
</html>