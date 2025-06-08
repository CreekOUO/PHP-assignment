<?php
session_start(); // 啟用 Session

// 清除所有 Session 變數
$_SESSION = array();

//清除 Session
session_destroy();

header("Location: login.php");
exit();
?>
