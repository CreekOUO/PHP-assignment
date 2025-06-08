<?php
session_start();
include("dbConnect.php"); 
$error = "";
//頁面和表單提交
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = mysqli_prepare($link, $sql);
    mysqli_stmt_bind_param($stmt, "s", $email);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);//得到結果集

    if ($user = mysqli_fetch_assoc($result)) {//取出查詢出的一筆資料
        if (password_verify($password, $user['password'])) {//比對密碼
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'email' => $user['email'],
                'role' => $user['role']   
            ];
            header("Location: materialsPage.php");
            exit();
        } else {
            $error = "密碼錯誤";
        }
    } else {
        $error = "找不到這個帳號";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">
<head>
    <meta charset="UTF-8">
    <title>登入 - BrushShare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body >
<?php include("header.inc.php"); ?>
<div class="container mt-5" style="max-width: 450px;">
    <h2 class="text-center mb-4">登入帳號</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form action="" method="post">
        <div class="form-floating mb-3">
            <input type="email" class="form-control" id="email" name="email" placeholder="email@example.com" required>
            <label for="email">電子郵件</label>
        </div>

        <div class="form-floating mb-4">
            <input type="password" class="form-control" id="password" name="password" placeholder="密碼" required>
            <label for="password">密碼</label>
        </div>

        <button type="submit" class="btn btn-dark w-100">登入</button>
        <p class="mt-3 text-center">還沒有帳號？<a href="signup.php">註冊</a></p>
    </form>
</div>

</body>
</html>
