<?php
include '../config/db.php';

$error = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $password = $_POST['password'];

    $result = mysqli_query($conn, "SELECT * FROM users WHERE email='$email'");
    $user = mysqli_fetch_assoc($result);

    if ($user && password_verify($password, $user['password'])) {
        // URL එකට user_id දාලා යමු
        header("Location: /billing-simple/create_bill.php?user_id=" . $user['id'] . "&name=" . urlencode($user['name']));
        exit();
    } else {
        $error = "Email හෝ Password වැරදියි!";
    }
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>Login</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5" style="max-width: 400px;">
    <div class="card shadow">
        <div class="card-header bg-primary text-white">
            <h4 class="mb-0">🔑 Login</h4>
        </div>
        <div class="card-body">
            <?php if($error) echo "<div class='alert alert-danger'>$error</div>"; ?>
            <form method="POST">
                <div class="mb-3">
                    <label>Email</label>
                    <input type="email" name="email" class="form-control" value="admin@gmail.com" required>
                </div>
                <div class="mb-3">
                    <label>Password</label>
                    <input type="password" name="password" class="form-control" value="123456" required>
                </div>
                <button type="submit" class="btn btn-primary w-100">Login</button>
            </form>
            <small class="text-muted mt-2 d-block">Demo: admin@gmail.com / 123456</small>
        </div>
    </div>
</div>
</body>
</html>