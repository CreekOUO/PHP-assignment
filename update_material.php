<?php
include("dbConnect.php");

$material_id = isset($_GET['id']) ? intval($_GET['id']) : 0;

// 載入原素材資料
$select_sql = "SELECT * FROM materials WHERE material_id = ?";
$stmt = mysqli_prepare($link, $select_sql);
mysqli_stmt_bind_param($stmt, "i", $material_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$material = mysqli_fetch_assoc($result);

if (!$material) {
    echo "找不到素材。";
    exit();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $description = trim($_POST['description']);
    $categories = trim($_POST['categories']);

    $filename = $material['filename'];
    $filepath = $material['filepath'];
    $software = $material['software'];
    $coverpath = $material['coverpath'];

    // 如果上傳了新素材檔案
    if (!empty($_FILES['filepath']['name'])) {
        // 刪除舊素材檔案
        if (!empty($material['filepath']) && file_exists($material['filepath'])) {
            unlink($material['filepath']);
        }
        $filename = basename($_FILES["filepath"]["name"]);
        $filepath = "uploads/materials/" . $filename;

        // 資料夾步存在創一個
        if (!is_dir("uploads/materials")) {
            mkdir("uploads/materials", 0777, true);
        }

        // 移動檔案+檢查
        if (!move_uploaded_file($_FILES["filepath"]["tmp_name"], $filepath)) {
            echo "素材檔案上傳失敗，請檢查 uploads/materials 資料夾權限。";
            exit();
        }
        // 自動依副檔名放software
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
    }

    // 如果上傳了新封面圖
    if (!empty($_FILES['coverpath']['name'])) {
        // 刪除舊檔
        if (!empty($material['coverpath']) && file_exists($material['coverpath'])) {
            unlink($material['coverpath']);
        }

        $cover_filename = basename($_FILES["coverpath"]["name"]);
        $coverpath = "uploads/covers/" . $cover_filename;
        // 確保資料夾存在
        if (!is_dir("uploads/covers")) {
            mkdir("uploads/covers", 0777, true);
        }

        // 移動檔案
        if (!move_uploaded_file($_FILES["coverpath"]["tmp_name"], $coverpath)) {
            echo " 封面圖上傳失敗。";
            exit();
        }
    }


    // 更新資料庫
    $update_sql = "UPDATE materials SET title=?, description=?, categories=?, software=?, filepath=?, filename=?, coverpath=? WHERE material_id=?";
    $stmt = mysqli_prepare($link, $update_sql);
    mysqli_stmt_bind_param(
        $stmt,
        "sssssssi",
        $title,
        $description,
        $categories,
        $software,
        $filepath,
        $filename,
        $coverpath,
        $material_id
    );
    mysqli_stmt_execute($stmt);

    header("Location: profile.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="zh-Hant">

<head>
    <meta charset="UTF-8">
    <title>編輯素材</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.4/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">
    <div class="container mt-5" style="max-width: 600px;">
        <h2>編輯素材</h2>

        <!-- 顯示封面 -->
        <div class="mb-3">
            <label class="form-label">目前封面圖</label><br>
            <img src="<?= htmlspecialchars($material['coverpath']) ?>"
                style="max-width: 100%; border: 1px solid #ccc;">
        </div>

        <form method="post" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">標題</label>
                <input type="text" name="title" class="form-control" value="<?= htmlspecialchars($material['title']) ?>"
                    required>
            </div>

            <div class="mb-3">
                <label class="form-label">簡介</label>
                <textarea name="description"
                    class="form-control"><?= htmlspecialchars($material['description']) ?></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">更換素材檔案</label>
                <input type="file" name="filepath" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">更換封面圖片</label>
                <input type="file" name="coverpath" class="form-control">
            </div>

            <div class="mb-3">
                <label class="form-label">分類</label>
                <select name="categories" class="form-select" required>
                    <?php
                    $options = ["Brush", "Texture", "Pattern", "Manga", "Template", "Drawing File"];
                    foreach ($options as $opt) {
                        $selected = ($material['categories'] === $opt) ? 'selected' : '';
                        echo "<option value=\"$opt\" $selected>$opt</option>";
                    }
                    ?>
                </select>
            </div>

            <div class="d-flex gap-2">
                <a href="profile.php" class="btn btn-secondary">取消</a>
                <button type="submit" class="btn btn-primary">儲存變更</button>
            </div>
        </form>
    </div>
</body>

</html>