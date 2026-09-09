<?php
include 'config/db.php';

// URL එකෙන් User Data ගන්න
if (!isset($_GET['user_id']) || !isset($_GET['name'])) {
    header("Location: auth/login.php");
    exit();
}
$user_id = intval($_GET['user_id']);
$user_name = htmlspecialchars($_GET['name']);
?>
<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Billing System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        body { background: #f0f2f5; }
        .card-custom { background: white; border-radius: 12px; padding: 20px; box-shadow: 0 2px 10px rgba(0,0,0,0.08); }
        @media print { .no-print { display: none !important; } }
        .table-cart td { vertical-align: middle; }
        .summary-box { background: #f8f9fa; border-radius: 10px; padding: 15px; }
    </style>
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark no-print">
    <div class="container">
        <a class="navbar-brand" href="#"><i class="fas fa-cash-register"></i> POS</a>
        <div class="ms-auto">
            <span class="text-light me-3">👋 <?php echo $user_name; ?></span>
            <a href="history.php" class="btn btn-outline-info btn-sm me-2"><i class="fas fa-history"></i> History</a>
            <a href="auth/logout.php" class="btn btn-danger btn-sm"><i class="fas fa-sign-out-alt"></i> Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-3">
    <div class="row">
        <!-- LEFT: Cart -->
        <div class="col-lg-8">
            <div class="card-custom">
                <h5><i class="fas fa-shopping-cart text-success"></i> Current Bill</h5>
                <div id="cart-container">
                    <?php include 'ajax/get_cart_html.php'; ?>
                </div>

                <!-- Add Product -->
                <div class="row g-2 mt-3 no-print">
                    <div class="col-md-5">
                        <input list="product-list" id="product-search" class="form-control" placeholder="Search product...">
                        <datalist id="product-list">
                            <?php
                            $products = mysqli_query($conn, "SELECT id, name, price FROM products ORDER BY name");
                            while ($p = mysqli_fetch_assoc($products)) {
                                echo "<option value='{$p['name']}' data-id='{$p['id']}' data-price='{$p['price']}'>Rs. {$p['price']}</option>";
                            }
                            ?>
                        </datalist>
                        <input type="hidden" id="selected-product-id">
                        <input type="hidden" id="selected-product-price">
                    </div>
                    <div class="col-md-2">
                        <input type="number" id="item-qty" class="form-control" value="1" min="1">
                    </div>
                    <div class="col-md-2">
                        <button class="btn btn-success w-100" onclick="addToCart()"><i class="fas fa-plus"></i> Add</button>
                    </div>
                    <div class="col-md-3 text-end">
                        <button class="btn btn-outline-danger" onclick="clearCart()"><i class="fas fa-trash"></i> Clear</button>
                    </div>
                </div>
            </div>
        </div>

        <!-- RIGHT: Summary -->
        <div class="col-lg-4">
            <div class="card-custom summary-box">
                <h6><i class="fas fa-credit-card"></i> Payment</h6>
                <hr>

                <div class="mb-2">
                    <label class="form-label small">Customer</label>
                    <select id="customer-id" class="form-select form-select-sm">
                        <option value="">Walk-in</option>
                        <?php
                        $custs = mysqli_query($conn, "SELECT id, name FROM customers");
                        while ($c = mysqli_fetch_assoc($custs)) {
                            echo "<option value='{$c['id']}'>{$c['name']}</option>";
                        }
                        ?>
                    </select>
                </div>

                <div id="summary">
                    <div class="d-flex justify-content-between"><span>Total:</span> <strong id="display-total">Rs. 0.00</strong></div>
                    <div class="d-flex justify-content-between mt-1">
                        <span>Discount %</span>
                        <input type="number" id="discount-percent" class="form-control form-control-sm w-50 text-end" value="0" min="0" max="100" oninput="updateSummary()">
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span>Net:</span> <strong id="display-net">Rs. 0.00</strong>
                    </div>
                    <div class="d-flex justify-content-between mt-1">
                        <span>Paid:</span>
                        <input type="number" id="paid-amount" class="form-control form-control-sm w-50 text-end" value="0" step="10" oninput="updateSummary()">
                    </div>
                    <div class="d-flex justify-content-between mt-2 border-top pt-2">
                        <span class="fw-bold">Change:</span>
                        <span class="fw-bold text-success" id="display-change">Rs. 0.00</span>
                    </div>
                </div>

                <hr class="no-print">
                <div class="d-grid gap-2 no-print">
                    <button class="btn btn-primary" onclick="saveBill()"><i class="fas fa-save"></i> Save Bill</button>
                    <button class="btn btn-warning" onclick="window.print()"><i class="fas fa-print"></i> Print</button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
// Product Search Auto-fill
document.getElementById('product-search').addEventListener('input', function() {
    const val = this.value;
    const options = document.querySelectorAll('#product-list option');
    let found = false;
    options.forEach(opt => {
        if (opt.value === val) {
            document.getElementById('selected-product-id').value = opt.dataset.id;
            document.getElementById('selected-product-price').value = opt.dataset.price;
            found = true;
        }
    });
    if (!found) {
        document.getElementById('selected-product-id').value = '';
        document.getElementById('selected-product-price').value = '';
    }
});

function addToCart() {
    const pid = document.getElementById('selected-product-id').value;
    const qty = document.getElementById('item-qty').value;
    if (!pid) { alert('Select a product!'); return; }

    fetch('ajax/add_to_cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: `product_id=${pid}&qty=${qty}`
    })
    .then(res => res.text())
    .then(html => {
        document.getElementById('cart-container').innerHTML = html;
        updateSummary();
        document.getElementById('product-search').value = '';
        document.getElementById('selected-product-id').value = '';
        document.getElementById('item-qty').value = 1;
    });
}

function removeFromCart(key) {
    if (!confirm('Remove this item?')) return;
    fetch(`ajax/remove_from_cart.php?key=${key}`)
    .then(res => res.text())
    .then(html => {
        document.getElementById('cart-container').innerHTML = html;
        updateSummary();
    });
}

function clearCart() {
    if (!confirm('Clear all items?')) return;
    fetch('ajax/remove_from_cart.php?clear=1')
    .then(res => res.text())
    .then(html => {
        document.getElementById('cart-container').innerHTML = html;
        updateSummary();
    });
}

function updateSummary() {
    fetch('ajax/get_cart_totals.php')
    .then(res => res.json())
    .then(data => {
        const total = data.total || 0;
        const discPct = parseFloat(document.getElementById('discount-percent').value) || 0;
        const discAmt = (total * discPct) / 100;
        const net = total - discAmt;
        const paid = parseFloat(document.getElementById('paid-amount').value) || 0;
        const change = paid - net;

        document.getElementById('display-total').innerText = 'Rs. ' + total.toFixed(2);
        document.getElementById('display-net').innerText = 'Rs. ' + net.toFixed(2);
        document.getElementById('display-change').innerText = 'Rs. ' + change.toFixed(2);
    });
}

function saveBill() {
    const netText = document.getElementById('display-net').innerText.replace('Rs. ', '');
    const net = parseFloat(netText);
    const paid = parseFloat(document.getElementById('paid-amount').value) || 0;

    if (paid < net) {
        alert('Paid amount must be >= Net amount!');
        return;
    }

    if (!confirm('Save this bill?')) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = 'save_bill.php';
    const fields = {
        discount_percent: document.getElementById('discount-percent').value,
        paid_amount: document.getElementById('paid-amount').value,
        customer_id: document.getElementById('customer-id').value,
        user_id: '<?php echo $user_id; ?>'
    };
    for (let k in fields) {
        const inp = document.createElement('input');
        inp.type = 'hidden';
        inp.name = k;
        inp.value = fields[k];
        form.appendChild(inp);
    }
    document.body.appendChild(form);
    form.submit();
}

// Auto update summary on load
window.onload = function() {
    updateSummary();
    setInterval(updateSummary, 3000);
};
</script>

</body>
</html>