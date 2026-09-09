<?php
include 'config/db.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Bill History</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<div class="container mt-4">
    <div class="d-flex justify-content-between">
        <h3>📋 Bill History</h3>
        <a href="create_bill.php?user_id=<?php echo $_GET['user_id'] ?? 1; ?>&name=Admin" class="btn btn-primary">New Bill</a>
    </div>
    <table class="table table-striped table-bordered mt-3">
        <thead class="table-dark">
            <tr><th>Bill No</th><th>Customer</th><th>Net Total</th><th>Paid</th><th>Date</th><th>Action</th></tr>
        </thead>
        <tbody>
        <?php
        $bills = mysqli_query($conn, "SELECT b.*, c.name as cust FROM bills b LEFT JOIN customers c ON b.customer_id = c.id ORDER BY b.id DESC");
        while ($row = mysqli_fetch_assoc($bills)) {
            echo "<tr>
                <td><strong>{$row['bill_no']}</strong></td>
                <td>" . ($row['cust'] ?? 'Walk-in') . "</td>
                <td>Rs. {$row['net_amount']}</td>
                <td>Rs. {$row['paid_amount']}</td>
                <td>{$row['created_at']}</td>
                <td><a href='view_bill.php?bill_id={$row['id']}' class='btn btn-sm btn-info'>View</a></td>
            </tr>";
        }
        ?>
        </tbody>
    </table>
</div>
</body>
</html>