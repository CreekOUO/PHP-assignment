<?php
session_start();
include("dbConnect.php");

// 確認是否登入
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}
$user_id = $_SESSION['user']['id'];
$material_id = intval($_POST['material_id']);
$author_id = intval($_POST['author_id']);

// 檢查是否已經點過讚
$check_sql = "SELECT id FROM likes WHERE user_id = ? AND material_id = ?";
$check_stmt = mysqli_prepare($link, $check_sql);
mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $material_id);
mysqli_stmt_execute($check_stmt);
mysqli_stmt_store_result($check_stmt);//存到buffer，結果會存入 PHP RAM

if (mysqli_stmt_num_rows($check_stmt) > 0) {
    // 已點讚 → 刪除
    $delete_sql = "DELETE FROM likes WHERE user_id = ? AND material_id = ?";
    $delete_stmt = mysqli_prepare($link, $delete_sql);
    mysqli_stmt_bind_param($delete_stmt, "ii", $user_id, $material_id);
    mysqli_stmt_execute($delete_stmt);
} else {
    // 未點讚 → 新增
    $insert_sql = "INSERT INTO likes (user_id, author_id, material_id) VALUES (?, ?, ?)";
    $insert_stmt = mysqli_prepare($link, $insert_sql);
    mysqli_stmt_bind_param($insert_stmt, "iii", $user_id, $author_id, $material_id);
    mysqli_stmt_execute($insert_stmt);
}

// 返回
header("Location: materialsPage.php");
exit();
?>