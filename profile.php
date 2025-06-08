<?php
session_start();
include("dbConnect.php");

// 確認是否登入
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
// like 數量
$like_sql = "SELECT COUNT(*) AS total_likes FROM likes WHERE author_id = ?";
$like_stmt = mysqli_prepare($link, $like_sql);
mysqli_stmt_bind_param($like_stmt, "i", $user_id);
mysqli_stmt_execute($like_stmt);
$like_result = mysqli_stmt_get_result($like_stmt);
$like_data = mysqli_fetch_assoc($like_result);
$total_likes = $like_data['total_likes'];
// 抓出使用者上傳的素材
$sql = "SELECT * FROM materials WHERE author = ? ORDER BY upload_date DESC";
$stmt = mysqli_prepare($link, $sql);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>我的素材 - BrushShare</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        footer {
            position: fixed;
            bottom: 0;
            width: 100%;
            height: 50px;
            line-height: 50px;
            text-align: center;
            background-color: rgb(119, 119, 119);
            color: rgb(246, 240, 255);
            border-top: 1px solid #ccc;
            z-index: 100;
        }
    </style>
</head>

<body style="margin-bottom: 50px;">
    <?php include("header.inc.php"); ?>
    <div class="container mt-5">

        <h2 class="mb-4">我的上傳素材</h2>

        <?php if (mysqli_num_rows($result) > 0): ?>
            <div class="row row-cols-1 row-cols-md-3 g-4">
                <?php while ($row = mysqli_fetch_assoc($result)): ?>
                    <div class="col">
                        <div class="card h-100 p-0 border">
                            <!-- 點擊圖片可進入詳細頁 -->
                            <a href="material_detail.php?id=<?= $row['material_id'] ?>">
                                <img src="<?= htmlspecialchars($row['coverpath']) ?>" alt="封面" class="card-img-top"
                                    style="width: 100%; height: 200px; object-fit: cover; display: block; border-radius: 0;">
                            </a>

                            <div class="card-body">
                                <h5 class="card-title mb-2">
                                    <a href="material_detail.php?id=<?= $row['material_id'] ?>"
                                        class="text-decoration-none text-dark">
                                        <?= htmlspecialchars($row['title']) ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted mb-3">上傳時間：<?= $row['upload_date'] ?></p>

                                <div class="d-flex gap-2">
                                    <a href="update_material.php?id=<?= $row['material_id'] ?>"
                                        class="btn btn-outline-primary btn-sm">編輯</a>
                                    <a href="delete_material.php?id=<?= $row['material_id'] ?>"
                                        class="btn btn-outline-danger btn-sm" onclick="return confirm('確定要刪除嗎？');">刪除</a>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>你尚未上傳任何素材。</p>
        <?php endif; ?>
    </div>
    <footer>
        <p>你所有素材共獲得 <?= $total_likes ?> 個讚</p>
    </footer </body>

</html>