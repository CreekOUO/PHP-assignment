<?php
session_start();
include("dbConnect.php");

if (!isset($_SESSION['user'])) {
    header("Location: login.html");
    exit();
}

// 接收表單資料
$author = $_SESSION['user']['id'];
$title = $_POST['title'];
$description = $_POST['description'];
$category = $_POST['category'];

// 建立 uploads 資料夾
$upload_dir = "uploads/";
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0755, true);
}

// 處理封面圖片
$cover_name = basename($_FILES["coverpath"]["name"]);
$cover_path = $upload_dir . time() . "_cover_" . $cover_name;
move_uploaded_file($_FILES["coverpath"]["tmp_name"], $cover_path);

// 處理素材檔案
$filename = basename($_FILES["filename"]["name"]);
$filepath = $upload_dir . time() . "_file_" . $filename ;
move_uploaded_file($_FILES["filename"]["tmp_name"], $filepath);

$ft = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
switch ($ft) {
    case "abr":
        $software = "Photoshop";
        break;
    case "sut":
        $software = "Clip Studio";
        break;
    case "brushset":
        $software = "Procreate";
        break;
    default:
        $software = NULL;
}
// 寫入資料庫
$sql = "INSERT INTO materials (author, title, description, coverpath, filepath, filename, categories, software)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$stmt = mysqli_prepare($link, $sql);//預處理，會把 SQL 查詢的結構與參數分開處理，參數只當作資料傳入
mysqli_stmt_bind_param($stmt, "isssssss", $author, $title, $description, $cover_path, $filepath, $filename, $category, $software);
mysqli_stmt_execute($stmt);

// 成功後導回主畫面
header("Location: materialsPage.php");
exit();
?>