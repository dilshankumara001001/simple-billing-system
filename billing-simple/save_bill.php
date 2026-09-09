<?php
// Error Reporting ඔන් කරමු - හරියටම බලමු මොකද වෙන්නේ කියලා
error_reporting(E_ALL);
ini_set('display_errors', 1);

include 'config/db.php';

echo "<h2>🔍 Save Bill Debugger</h2>";

// 1. POST එකෙන් Data ආවද?
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    die("❌ මෙය POST Request එකක් නෙවෙයි. කරුණාකර Form එකෙන් Save කරන්න.");
}

// 2. Cart එකේ Items තියෙනවද?
if (empty($_SESSION['cart'])) {
    die("❌ Cart එක හිස්යි. කරුණාකර අයිතම එකතු කරන්න.");
}

// 3. POST Data ගන්න
$user_id = intval($_POST['user_id'] ?? 0);
$customer_id = intval($_POST['customer_id'] ?? 0);
$discount_percent = floatval($_POST['discount_percent'] ?? 0);
$paid_amount = floatval($_POST['paid_amount'] ?? 0);

echo "<p>User ID: $user_id</p>";
echo "<p>Customer ID: $customer_id</p>";
echo "<p>Discount: $discount_percent%</p>";
echo "<p>Paid: $paid_amount</p>";

// 4. එකතුව ගණනය කරන්න
$total_amount = 0;
foreach ($_SESSION['cart'] as $item) {
    $total_amount += $item['total'];
}
echo "<p>Total Amount: $total_amount</p>";

$discount_amount = ($total_amount * $discount_percent) / 100;
$net_amount = $total_amount - $discount_amount;
$change_amount = $paid_amount - $net_amount;

echo "<p>Net Amount: $net_amount</p>";

// 5. Bill Number එක හදන්න
$bill_no = 'INV-' . date('Ymd') . '-' . rand(1000, 9999);
echo "<p>Bill No: $bill_no</p>";

// 6. Insert Query එක (customer_id NULL නම් හරියට දාන්න)
if ($customer_id > 0) {
    $customer_sql = $customer_id;
} else {
    $customer_sql = 'NULL';
}

$query = "INSERT INTO bills (bill_no, user_id, customer_id, total_amount, discount_percent, discount_amount, net_amount, paid_amount, change_amount) 
          VALUES ('$bill_no', $user_id, $customer_sql, $total_amount, $discount_percent, $discount_amount, $net_amount, $paid_amount, $change_amount)";

echo "<p><strong>SQL Query:</strong> " . htmlspecialchars($query) . "</p>";

// 7. Query එක Run කරන්න
if (mysqli_query($conn, $query)) {
    $bill_id = mysqli_insert_id($conn);
    echo "<p style='color:green;'>✅ Bill Header එක Insert වුණා! Bill ID: $bill_id</p>";

    // 8. Bill Items Insert කරන්න
    foreach ($_SESSION['cart'] as $item) {
        $product_id = $item['product_id'];
        $qty = $item['qty'];
        $price = $item['price'];
        $total = $item['total'];

        $item_query = "INSERT INTO bill_items (bill_id, product_id, qty, price, total) 
                       VALUES ($bill_id, $product_id, $qty, $price, $total)";
        
        if (mysqli_query($conn, $item_query)) {
            echo "<p style='color:green;'>✅ Item Insert වුණා: {$item['name']}</p>";
        } else {
            echo "<p style='color:red;'>❌ Item Insert Error: " . mysqli_error($conn) . "</p>";
            exit();
        }
    }

    // 9. Cart එක හිස් කරන්න
    $_SESSION['cart'] = [];
    echo "<p>✅ Cart එක හිස් කරන ලදි.</p>";

    // 10. View Bill පිටුවට Redirect කරන්න
    header("Location: view_bill.php?bill_id=" . $bill_id);
    exit();

} else {
    // මෙතනට ආවොත් තමයි වැරදිලා කියන්නේ
    echo "<p style='color:red; font-weight:bold;'>❌ Bill Header Insert වැරදුණා!</p>";
    echo "<p><strong>MySQL Error:</strong> " . mysqli_error($conn) . "</p>";
    echo "<p><strong>Query:</strong> " . htmlspecialchars($query) . "</p>";
}
?>