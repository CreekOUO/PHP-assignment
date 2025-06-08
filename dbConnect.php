<?php
$link = @mysqli_connect("localhost", "root", "") or die("無法連線資料庫！<br/>");
mysqli_select_db($link, "brushshare");
?>