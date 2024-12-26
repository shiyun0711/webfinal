<?php
// 資料庫連線參數
$servername = "localhost"; // 主機名稱
$username = "root"; // 資料庫帳號
$password = ""; // 資料庫密碼（若無密碼則為空字串）
$dbname = "final"; // 資料庫名稱

// 建立連線
$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連線是否成功
if ($conn->connect_error) {
    die("資料庫連線失敗：" . $conn->connect_error);
}
?>