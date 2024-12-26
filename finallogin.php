<?php
require_once "finaldb.php";
$msg = $_GET["msg"] ?? "";

if ($_POST) {
    $account = $_POST["account"] ?? "";
    $password = $_POST["password"] ?? "";

    if (!empty($account) && !empty($password)) {
        $sql = "SELECT * FROM users WHERE account = ? AND password = ?";
        $stmt = mysqli_prepare($conn, $sql);
        mysqli_stmt_bind_param($stmt, 'ss', $account, $password);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if ($row = mysqli_fetch_assoc($result)) {
            session_start();
            $_SESSION["account"] = $account;
            $_SESSION["role"] = $row["role"]; // 記錄role
            header("Location: finalindex.php");
            exit;
        } else {
            header("Location: finallogin.php?msg=帳號或密碼錯誤");
            exit;
        }
    } else {
        header("Location: finallogin.php?msg=請輸入帳號和密碼");
        exit;
    }
}
?>

<?php require_once "finalheader.php" ?>

<div class="container">
    <h2 class="my-4">登入帳號</h2>
    <form action="finallogin.php" method="post">
        <input placeholder="帳號" class="form-control" type="text" name="account" required><br>

        <!-- 密碼欄位，加入顯示/隱藏密碼的功能 -->
        <div class="input-group mb-3">
            <input placeholder="密碼" class="form-control" type="password" name="password" id="password" required><br>
            <button type="button" class="btn btn-outline-secondary" onclick="togglePassword()">顯示/隱藏密碼</button>
        </div>

        <input class="btn btn-primary" type="submit" value="登入">
        <p class="text-danger"><?=$msg?></p> <!-- 顯示錯誤訊息 -->
    </form>
    <p>還沒有帳號？ <a href="finalregister.php">註冊</a></p> <!-- 註冊連結 -->
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
