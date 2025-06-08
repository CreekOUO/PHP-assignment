<?php
session_start();
include("dbConnect.php"); // 你自己的資料庫連線檔案

$err = '';
$success = '';

// 表單送出時處理
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $username = trim($_POST["username"]);
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm = $_POST["confirm"];

    // 驗證
    if ($password !== $confirm) {
        $err = "密碼與確認密碼不一致";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $err = "Email 格式不正確";
    } else {
        // 檢查 email 是否已存在
        $check_sql = "SELECT id FROM users WHERE email = ?";
        $stmt = mysqli_prepare($link, $check_sql);
        mysqli_stmt_bind_param($stmt, "s", $email);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);

        if (mysqli_stmt_num_rows($stmt) > 0) {
            $err = "這個 Email 已被註冊";
        } else {
            // 寫入
            $hash = password_hash($password, PASSWORD_DEFAULT);
            $insert_sql = "INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'general')";
            $stmt = mysqli_prepare($link, $insert_sql);
            mysqli_stmt_bind_param($stmt, "sss", $username, $email, $hash);
            mysqli_stmt_execute($stmt);

            $success = "註冊成功！";
            header("refresh:3;url=login.php");
        }
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>註冊 - BrushShare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>

    <?php include("header.inc.php"); ?>
    <div class="container mt-5" style="max-width: 450px;">
        <h2 class="text-center mb-4">註冊帳號</h2>

        <?php if ($err): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
        <?php elseif ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>

        <form action="" method="post">
            <div class="form-floating mb-3">
                <input type="text" class="form-control" id="username" name="username" placeholder="使用者名稱" required>
                <label for="username">使用者名稱</label>
            </div>

            <div class="form-floating mb-3">
                <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com"
                    required>
                <label for="email">電子郵件</label>
            </div>

            <div class="form-floating mb-3">
                <input type="password" class="form-control" id="password" name="password" placeholder="密碼" required>
                <label for="password">密碼</label>
            </div>

            <div class="form-floating mb-4">
                <input type="password" class="form-control" id="confirm" name="confirm" placeholder="確認密碼" required>
                <label for="confirm">確認密碼</label>
            </div>

            <button type="submit" class="btn btn-dark w-100">註冊</button>
            <p class="mt-3 text-center">已有帳號？<a href="login.php">登入</a></p>
        </form>
    </div>

</body>

</html>