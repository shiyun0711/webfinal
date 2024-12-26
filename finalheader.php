<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 獲取當前頁面檔案名稱
$current_page = basename($_SERVER['PHP_SELF']);
?>
<html lang="en">
    <head>
        <meta charset="utf-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no" />
        <meta name="description" content="" />
        <meta name="author" content="" />
        <title>秀學輔</title>
        <!-- Favicon-->
        <link rel="icon" type="image/x-icon" href="assets/favicon.ico" />
        <!-- Custom Google font-->
        <link rel="preconnect" href="https://fonts.googleapis.com" />
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
        <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@100;200;300;400;500;600;700;800;900&amp;display=swap" rel="stylesheet" />
        <!-- Bootstrap icons-->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.1/font/bootstrap-icons.css" rel="stylesheet" />
        <!-- Core theme CSS (includes Bootstrap)-->
        <link href="css/styles.css" rel="stylesheet" />
    </head>

    <body>
    <nav class="navbar navbar-expand-lg navbar-light py-3" style="background-color: #6a1b9a;">
    <div class="container px-5">
        <a class="navbar-brand" href="finalindex.php"><span class="fw-bolder text-white">秀學輔</span></a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent" aria-expanded="false" aria-label="Toggle navigation"><span class="navbar-toggler-icon"></span></button>
        <div class="collapse navbar-collapse" id="navbarSupportedContent">
            <ul class="navbar-nav ms-auto mb-2 mb-lg-0 small fw-bolder">
                <li class="nav-item"><a class="nav-link text-white" href="finaluser.php">個人資料</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="finalachievement.php">學習成果</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="finallogout.php">登出</a></li>
            </ul>
        </div>
    </div>
</nav>

</body>