<?php
// 載入資料庫連接
require_once "finalheader.php";
require_once 'finaldb.php';

// 確認使用者是否已登入，並避免重複啟動 session
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // 確保 session 啟動
}

if (!isset($_SESSION["account"])) {
    header("Location: finallogin.php?msg=請先登入");
    exit;
}

$account = $_SESSION["account"];  // 從 session 中取得帳號

// 查詢資料庫中的使用者資料
$sql = "SELECT * FROM users WHERE account = ?";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, 's', $account);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($row = mysqli_fetch_assoc($result)) {
    $name = $row['name'];
    $password = $row['password'];
    $email = $row['email'];
    $bio = $row['bio'];
    $photo = $row['photo'];
} else {
    echo "使用者資料未找到";
    exit;
}

// 更新資料邏輯
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $newName = $_POST['name'] ?? $name;
    $newPassword = $_POST['password'] ?? $password;
    $newEmail = $_POST['email'] ?? $email;
    $newBio = $_POST['bio'] ?? $bio;

    // 更新圖片處理
    if (!empty($_FILES['photo']['name'])) {
        $targetDir = "uploads/";
        $photoName = basename($_FILES["photo"]["name"]);
        $targetFilePath = $targetDir . $photoName;

        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $targetFilePath)) {
            $newPhoto = $targetFilePath;
        } else {
            echo "圖片上傳失敗";
            $newPhoto = $photo; // 保留原本的圖片
        }
    } else {
        $newPhoto = $photo; // 保留原本的圖片
    }

    // 更新資料庫
    $updateSql = "UPDATE users SET name = ?, password = ?, email = ?, bio = ?, photo = ? WHERE account = ?";
    $updateStmt = mysqli_prepare($conn, $updateSql);
    mysqli_stmt_bind_param($updateStmt, 'ssssss', $newName, $newPassword, $newEmail, $newBio, $newPhoto, $account);

    if (mysqli_stmt_execute($updateStmt)) {
        header("Location: finaluser.php?msg=更新成功");
        exit;
    } else {
        echo "更新失敗";
    }
}
?>

<!-- 編輯表單 -->
<section class="py-5">
    <div class="container px-5 mb-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bolder mb-0"><span class="text-gradient d-inline">編輯個人資料</span></h1>
        </div>
        <div class="row gx-5 justify-content-center">
            <div class="col-lg-11 col-xl-9 col-xxl-8">
                <form action="finaledit.php" method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="name" class="form-label">姓名</label>
                        <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($name) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">密碼</label>
                        <input type="text" id="password" name="password" class="form-control" value="<?= htmlspecialchars($password) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="bio" class="form-label">簡介</label>
                        <textarea id="bio" name="bio" class="form-control" rows="4"><?= htmlspecialchars($bio) ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label for="photo" class="form-label">上傳新圖片</label>
                        <input type="file" id="photo" name="photo" class="form-control">
                        <?php if (!empty($photo)): ?>
                            <img src="<?= htmlspecialchars($photo) ?>" alt="使用者圖片" style="width: 150px; height: 200px;" class="mt-3">
                        <?php endif; ?>
                    </div>
                    <button type="submit" class="btn btn-primary">儲存變更</button>
                </form>
            </div>
        </div>
    </div>
</section>
