<?php
require_once 'finaldb.php';
session_start();

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_FILES['photo'])) {
    $image = $_FILES['photo'];
    $target_dir = "uploads/"; // 儲存圖片的資料夾
    $target_file = $target_dir . basename($image["name"]);

    // 移動圖片到指定資料夾
    if (move_uploaded_file($image["tmp_name"], $target_file)) {
        // 更新資料庫中的圖片路徑
        $account = $_SESSION["account"]; // 假設帳號存儲在 session 中
        $sql = "UPDATE users SET photo = ? WHERE account = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ss', $target_file, $account);

        if (mysqli_stmt_execute($stmt)) {
            header("Location: finaluser.php?msg=圖片上傳成功");
        } else {
            echo "圖片儲存失敗，請重試！";
        }
    } else {
        echo "圖片上傳失敗，請重試！";
    }
}
?>
