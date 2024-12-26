<?php
require_once "finaldb.php";
$msg = "";

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $account = trim($_POST["account"] ?? "");
    $password = trim($_POST["password"] ?? "");
    $email = trim($_POST["email"] ?? "");

    if (!empty($account) && !empty($password) && !empty($email)) {
        // 檢查帳號是否已存在
        $check_account_sql = "SELECT * FROM users WHERE account = ?";
        $stmt = mysqli_prepare($conn, $check_account_sql);
        mysqli_stmt_bind_param($stmt, 's', $account);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            $msg = "帳號已存在，請使用其他帳號";
        } else {
            // 檢查 email 是否已存在
            $check_email_sql = "SELECT * FROM users WHERE email = ?";
            $stmt = mysqli_prepare($conn, $check_email_sql);
            mysqli_stmt_bind_param($stmt, 's', $email);
            mysqli_stmt_execute($stmt);
            $result = mysqli_stmt_get_result($stmt);

            if (mysqli_num_rows($result) > 0) {
                $msg = "電子郵件已被註冊，請使用其他電子郵件";
            } else {
                // 直接使用明文密碼，不進行哈希處理
                $insert_sql = "INSERT INTO users (email, account, password) VALUES (?, ?, ?)";
                $stmt = mysqli_prepare($conn, $insert_sql);
                mysqli_stmt_bind_param($stmt, 'sss', $email, $account, $password);

                if (mysqli_stmt_execute($stmt)) {
                    header("Location: finallogin.php?msg=註冊成功，請登入");
                    exit;
                } else {
                    $msg = "註冊失敗，請重試";
                }
            }
        }
    } else {
        $msg = "請完整填寫所有欄位";
    }
}
?>

<?php require_once "finalheader.php"; ?>

<div class="container">
    <h2 class="my-4">註冊帳號</h2>
    <form action="finalregister.php" method="post">
        <div class="mb-3">
            <input placeholder="Email" class="form-control" type="email" name="email" required>
        </div>
        <div class="mb-3">
            <input placeholder="帳號" class="form-control" type="text" name="account" required>
        </div>

        <!-- 密碼欄位，加入顯示/隱藏密碼的功能 -->
        <div class="input-group mb-3">
            <input placeholder="密碼" class="form-control" type="password" name="password" id="password" required>
            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">顯示/隱藏密碼</button>
        </div>

        <div>
            <input class="btn btn-primary" type="submit" value="註冊">
        </div>

        <?php if ($msg): ?>
            <p class="text-danger mt-3"><?= htmlspecialchars($msg) ?></p> <!-- 顯示錯誤訊息 -->
        <?php endif; ?>
    </form>
</div>

<script>
    // 切換密碼顯示與隱藏
    function togglePassword() {
        var passwordField = document.getElementById("password");
        var passwordType = passwordField.type;
        if (passwordType === "password") {
            passwordField.type = "text";
        } else {
            passwordField.type = "password";
        }
    }
</script>
