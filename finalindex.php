<?php
require_once "finalheader.php";
require_once 'finaldb.php';


// 確認使用者是否已登入
if (!isset($_SESSION["account"])) {
    header("Location: finallogin.php?msg=請先登入");
    exit;
}
$account = $_SESSION["account"]; 
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
            <!-- Header-->
            <header class="py-5">
                <div class="container px-5 pb-5">
                    <div class="row gx-5 align-items-center">
                        <div class="col-xxl-5">
                            <!-- Header text content-->
                            <div class="text-center text-xxl-start">
                                <div class="fs-3 badge bg-gradient-primary-to-secondary text-white mb-5"><div class="text-uppercase">秀學輔 &middot; 秀出你的學習成果</div></div>
                                <h1 class="fs-1 fw-bolder mb-5"><span class="text-gradient d-inline">Show Your Skills, Shine Your Future!</span></h1>
                                <h2 class="my-4"><?= htmlspecialchars($name) ?></h2>
                                <div class="d-grid gap-3 d-sm-flex justify-content-sm-center justify-content-xxl-start mb-3">
                                    <a class="btn btn-outline-dark btn-lg px-5 py-3 fs-6 fw-bolder" href="finaluser.php">個人資料</a>
                                    <a class="btn btn-outline-dark btn-lg px-5 py-3 fs-6 fw-bolder" href="finalachievement.php">學習成果</a>
                                </div>
                            </div>
                        </div>
                        
                    </div>
                </div>
            </header>
            
        </main>
        
    </body>
</html>
