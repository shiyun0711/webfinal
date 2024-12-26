<?php
require_once "finaldb.php";
session_start();

// 確認使用者是否已登入
if (!isset($_SESSION["account"])) {
    header("Location: finallogin.php?msg=請先登入");
    exit;
}

// 處理表單提交
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $account = $_SESSION["account"];
    $certifications_name = !empty(trim($_POST['certifications_name'])) ? trim($_POST['certifications_name']) : NULL;
    $certifications_experience = !empty(trim($_POST['certifications_experience'])) ? trim($_POST['certifications_experience']) : NULL;

    // 圖片上傳
    $certificate_image = NULL;
    if (isset($_FILES['certificate_image']) && $_FILES['certificate_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0777, true); // 確保目錄存在
        }

        $certificate_image_name = basename($_FILES['certificate_image']['name']);
        $certificate_image_path = $upload_dir . uniqid() . "_" . $certificate_image_name;

        if (move_uploaded_file($_FILES['certificate_image']['tmp_name'], $certificate_image_path)) {
            $certificate_image = $certificate_image_path;
        }
    }

    // 插入新記錄
    $insert_query = "
        INSERT INTO achievements (
            account, certifications_name, certifications_experience, certificate_image
        ) VALUES (?, ?, ?, ?)
    ";
    $stmt_insert = $conn->prepare($insert_query);
    $stmt_insert->bind_param(
        "ssss",
        $account,
        $certifications_name,
        $certifications_experience,
        $certificate_image
    );

    if ($stmt_insert->execute()) {
        header("Location: finalachievement.php?msg=證書證照已成功新增");
        exit;
    } else {
        echo "新增失敗，請重試！";
    }
}
?>


<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增證書證照</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container px-5 my-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bolder">新增證書證照</h1>
            <p class="text-muted">請填寫以下欄位以新增您的證書證照。</p>
        </div>
        <form action="add_certification.php" method="POST" enctype="multipart/form-data">
            
            
            
            <!-- 證書證照 -->
            <h2 class="fw-bolder mb-3">證書證照</h2>
            <div class="mb-4">
                <label for="certifications_name" class="form-label">證書名稱：</label>
                <input type="text" id="certifications_name" name="certifications_name" class="form-control" placeholder="請輸入證書名稱">
            </div>
            <div class="mb-4">
                <label for="certifications_experience" class="form-label">心得：</label>
                <textarea id="certifications_experience" name="certifications_experience" class="form-control" placeholder="請輸入心得"></textarea>
            </div>
            
            <!-- 圖片上傳 -->
            <div class="mb-3">
                <label for="certificate_image" class="form-label">上傳圖片</label>
                <input type="file" id="certificate_image" name="certificate_image" class="form-control">
            </div>

            <button type="submit" class="btn btn-primary">新增</button>
        </form>
    </div>
</body>
</html>
