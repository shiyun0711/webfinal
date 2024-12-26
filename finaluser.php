<?php
// 載入資料庫連接
require_once "finalheader.php";
require_once 'finaldb.php';

// 確認使用者是否已登入，並且避免重複啟動 session
if (session_status() === PHP_SESSION_NONE) {
    session_start();  // 確保 session 啟動
}

// 確認使用者是否已登入
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
    $name = $row['name'];   // 假設資料庫中有 name 欄位
    $password = $row['password'];
    $email = $row['email'];
    $bio = $row['bio'];
    $photo = $row['photo'];
    // 如果有其他欄位也可以顯示
} else {
    echo "使用者資料未找到";
}


?>

<!-- 顯示資料 -->
<section class="py-5">
    <div class="container px-5 mb-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bolder mb-0"><span class="text-gradient d-inline">個人資料</span></h1>
        </div>
        <div class="row gx-5 justify-content-center">
            <div class="col-lg-11 col-xl-9 col-xxl-8">
                <div class="d-flex align-items-center justify-content-between mb-4">
                    <!-- 按鈕置於右側 -->
                    <a class="btn btn-dark px-4 py-3 ms-auto" href="finaledit.php">
                        <div class="d-inline-block bi bi-pencil me-2">
                            編輯
                        </div>
                    </a>
                </div>

                <!-- 顯示使用者的資料 -->
                <div class="card overflow-hidden shadow rounded-4 border-0 mb-5">
    <div class="card-body p-0">
        <div class="d-flex align-items-center">
            <div class="p-5">
                <h2 class="fw-bolder"><?= htmlspecialchars($name) ?></h2>
                <p><?= htmlspecialchars($bio) ?></p>
            </div>
            
            <!-- 顯示圖片 -->
            <?php if (!empty($photo)): ?>
                <img class="img-fluid ms-auto" src="<?= htmlspecialchars($photo) ?>" alt="使用者圖片" style="width: 300px; height: 400px;" />
            <?php else: ?>
                <img class="img-fluid ms-auto" src="assets/default-profile.png" alt="預設圖片" style="width: 300px; height: 400px;" />
            <?php endif; ?>
        </div>
    </div>
</div>


                <div class="mb-3">
                    <label for="id" class="form-label">帳號</label>
                    <input type="text" id="id" class="form-control" value="<?= htmlspecialchars($account) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">密碼</label>
                    <input type="text" id="password" name="password" class="form-control" value="<?= htmlspecialchars($password) ?>" readonly>
                </div>
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" id="email" name="email" class="form-control" value="<?= htmlspecialchars($email) ?>" readonly>
                </div>
            </div>
        </div>
    </div>
</section>
