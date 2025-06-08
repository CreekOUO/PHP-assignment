<?php
include("dbConnect.php");

$material_id = isset($_GET['id']) ? intval($_GET['id']) : 0;//intval()轉整數

if ($material_id > 0) {
    $delete_sql = "DELETE FROM materials WHERE material_id = ?";
    $stmt = mysqli_prepare($link, $delete_sql);
    mysqli_stmt_bind_param($stmt, "i", $material_id);
    mysqli_stmt_execute($stmt);
}

header("Location: profile.php");
exit();
?>
