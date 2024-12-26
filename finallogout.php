<?php
session_start();
session_destroy(); // 清除所有 Session 資料
header("Location: finallogin.php"); // 登出後跳轉到登入頁面
exit();
?>
