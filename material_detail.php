<?php
include("dbConnect.php");

$material_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 取資料
$sql = "SELECT m.*, u.username AS author_name 
        FROM materials m
        JOIN users u ON m.author = u.id
        WHERE material_id = ?";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $material_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$material = mysqli_fetch_assoc($result);
// 取得該素材的按讚數量
$like_sql = "SELECT COUNT(*) AS like_count FROM likes WHERE material_id = ?";
$like_stmt = mysqli_prepare($link, $like_sql);
mysqli_stmt_bind_param($like_stmt, "i", $material_id);
mysqli_stmt_execute($like_stmt);
$like_result = mysqli_stmt_get_result($like_stmt);
$like_data = mysqli_fetch_assoc($like_result);
$like_count = $like_data['like_count'];
if (!$material) {
    echo "找不到這筆素材。";
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title><?= htmlspecialchars($material['title']) ?> - 詳細介紹</title>
    <link rel="stylesheet" href="style.css">
</head>

<body>

    <div class="container" style="text-align: center; max-width: 800px; margin: auto;">
        <h1><?php echo htmlspecialchars($material['title']); ?></h1>
        <p>作者：<?php echo htmlspecialchars($material['author_name']); ?></p>
        <img src="<?php echo htmlspecialchars($material['coverpath']); ?>" alt="封面圖"
            style="max-width: 100%; height: auto; border: 1px solid #ccc;">

        <h4 class="mt-3">素材描述：</h4>
        <p><?php echo nl2br(htmlspecialchars($material['description'])); ?></p>

        <p><strong>分類：</strong><?php echo htmlspecialchars($material['categories']); ?></p>
        <p><strong>適用軟體：</strong><?php echo htmlspecialchars($material['software']) ?: '無指定'; ?></p>
        <p><strong>上傳時間：</strong><?php echo htmlspecialchars($material['upload_date']); ?></p>

        <hr>
        <a href="<?php echo htmlspecialchars($material['filepath']); ?>" download class="btn btn-success">下載素材</a>
        <a href="materialsPage.php" class="btn btn-secondary">返回素材首頁</a>
    </div>

</body>

</html>