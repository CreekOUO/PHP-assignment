<?php
session_start();
include("dbConnect.php");

$order = $_GET['order'] ?? 'date';
$softwareFilter = $_GET['softwareFilter'] ?? 'All';
$categoryFilter = $_GET['category'] ?? 'ALL';
$searchQuery = $_GET['q'] ?? '';

// 宣告陣列
$where = "";
$params = [];
$conditions = [];

// 搜尋條件
if (!empty($searchQuery)) {
    $conditions[] = "m.title LIKE ?";
    $params[] = "%" . $searchQuery . "%";
}

// 軟體篩選條件
if ($softwareFilter !== 'All') {
    $conditions[] = "m.software = ?";
    $params[] = $softwareFilter;
}

// 分類篩選條件
if ($categoryFilter !== 'ALL') {
    $conditions[] = "m.categories = ?";
    $params[] = $categoryFilter;
}

// 組合 WHERE 子句
if (!empty($conditions)) {
    $where = "WHERE " . implode(" AND ", $conditions);
}

// 排序條件
switch ($order) {
    case 'popularity':
        $orderBy = 'm.likes DESC';
        break;
    default:
        $orderBy = 'm.upload_date DESC';
        break;
}

// 組合 SQL 查詢語句
$sql = "SELECT m.*, u.username AS author_name
        FROM materials m
        JOIN users u ON m.author = u.id
        $where
        ORDER BY $orderBy";

// 執行查詢
$stmt = mysqli_prepare($link, $sql);
if (!empty($params)) {
    mysqli_stmt_bind_param($stmt, str_repeat("s", count($params)), ...$params);
}
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>BrushShare 素材首頁</title>
    <link rel="stylesheet" href="/endproject/style.css?v=123"> <!--每次加載最新CSS，我費勁千辛萬苦才發現更新CSS為甚麼套用不了的原因-->
</head>

<body>

    <?php include("header.inc.php"); ?>

    <div class="main">
        <aside class="sidebar">
            <h2 style="text-align: center; border-bottom: 1px solid #000;">Categories</h2>
            <ul style="text-decoration: none;">
                <li><a href="?category=ALL">ALL</a></li>
                <li><a href="?category=Brush">筆刷</a></li>
                <li><a href="?category=Texture">材質素材</a></li>
                <li><a href="?category=Pattern">圖案素材</a></li>
                <li><a href="?category=Manga">漫畫素材</a></li>
                <li><a href="?category=Template">設計模板</a></li>
                <li><a href="?category=Drawing File">繪圖檔案</a></li>
            </ul>

            <div style="margin-top: auto; text-align: center;">
                <a href="uploadPage.php" style="text-decoration: none;">
                    <div style="display: flex; justify-content: space-between; align-items: center;
                            padding: 10px 10px; background-color: rgb(245, 242, 249);
                            color: rgb(38, 39, 47); border: 2px solid #000; border-radius: 5px;
                            font-size: 24px; cursor: pointer;">
                        <span>Upload Your Love</span>
                        <img src="images/upload.png" alt="Upload Icon" style="width: 50px; height: 50px;">
                    </div>
                </a>
            </div>
        </aside>

        <section class="content">
            <form method="GET" class="filters">
                <label>Order by:
                    <select name="order">
                        <option value="date" <?= ($_GET['order'] ?? '') === 'date' ? 'selected' : '' ?>>Date Upload
                        </option>
                        <option value="popularity" <?= ($_GET['order'] ?? '') === 'popularity' ? 'selected' : '' ?>>
                            Popularity</option>
                    </select>
                </label>
                <label>Software select:
                    <select name="softwareFilter">
                        <option value="All" <?= ($_GET['softwareFilter'] ?? '') === 'All' ? 'selected' : '' ?>>All</option>
                        <option value="Photoshop" <?= ($_GET['softwareFilter'] ?? '') === 'Photoshop' ? 'selected' : '' ?>>
                            Photoshop</option>
                        <option value="Clip Studio" <?= ($_GET['softwareFilter'] ?? '') === 'Clip Studio' ? 'selected' : '' ?>>
                            Clip Studio</option>
                        <option value="Procreate" <?= ($_GET['softwareFilter'] ?? '') === 'Procreate' ? 'selected' : '' ?>>
                            Procreate</option>
                    </select>
                </label>
                <button type="submit">Apply</button>
            </form>
            <!--素材div  -->
            <div class="materials">
                <?php while ($material = mysqli_fetch_assoc($result)):

                    // 查素材的點讚數
                    $like_sql = "SELECT COUNT(*) AS like_count FROM likes WHERE material_id = ?";
                    $like_stmt = mysqli_prepare($link, $like_sql);
                    mysqli_stmt_bind_param($like_stmt, "i", $material['material_id']);
                    mysqli_stmt_execute($like_stmt);
                    $like_result = mysqli_stmt_get_result($like_stmt);
                    $like_data = mysqli_fetch_assoc($like_result);
                    $like_count = $like_data['like_count'] ?? 0;

                    // 查目前登入使用者是否有對這筆素材按讚
                    $liked = false;
                    if (isset($_SESSION['user'])) {
                        $user_id = $_SESSION['user']['id'];
                        $check_sql = "SELECT 1 FROM likes WHERE user_id = ? AND material_id = ?";
                        $check_stmt = mysqli_prepare($link, $check_sql);
                        mysqli_stmt_bind_param($check_stmt, "ii", $user_id, $material['material_id']);
                        mysqli_stmt_execute($check_stmt);
                        mysqli_stmt_store_result($check_stmt);
                        $liked = mysqli_stmt_num_rows($check_stmt) > 0;
                    }
                    ?>

                    <div class="material-box">
                        <!-- 詳細頁 -->
                        <a href="material_detail.php?id=<?= $material['material_id'] ?>"
                            style="text-decoration: none; color: inherit;">
                            <img src="<?= htmlspecialchars($material['coverpath']) ?>" alt="封面" class="material-cover">
                            <h3><?= htmlspecialchars($material['title']) ?></h3>
                            <p>by <?= htmlspecialchars($material['author_name']) ?></p>

                        </a>
                        <!--icon區-->
                        <div class="icons" style="">
                            <form action="like.php" method="post" style="display: inline;">
                                <input type="hidden" name="material_id" value="<?= $material['material_id'] ?>">
                                <input type="hidden" name="author_id" value="<?= $material['author'] ?>">
                                <button type="submit" style="border: none; background: none; cursor: pointer;">
                                    <img src="images/<?= $liked ? 'heart.png' : 'noheart.png' ?>?v=<?= $material['material_id']//強制圖片更新 ?>" 
                                        alt="Like"> 
                                </button>
                                <span><?= $like_count ?></span>
                            </form>
                            <img src="images/download.png" alt="Download">
                            <a href="<?= htmlspecialchars($material['filepath']) ?>" download>下載</a>
                            <!--general-->
                            <?php if (
                                isset($_SESSION['user']) &&
                                ($_SESSION['user']['role'] === 'administrator')
                            ): ?>
                                <a href="delete_material.php?id=<?= $material['material_id'] ?>"
                                    onclick="return confirm('確定要刪除這筆素材嗎？');">
                                    <img src="images/trashcan.png" alt="刪除" title="刪除"
                                        style="width: 24px; height: 24px; cursor: pointer;">
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        </section>
    </div>

</body>

</html>