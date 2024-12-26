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
    $subject = !empty(trim($_POST['subject'])) ? trim($_POST['subject']) : NULL;
    $programming_languages = !empty(trim($_POST['programming_languages'])) ? trim($_POST['programming_languages']) : NULL;
    $competitions_name = !empty(trim($_POST['competitions_name'])) ? trim($_POST['competitions_name']) : NULL;
    $competitions_award = !empty(trim($_POST['competitions_award'])) ? trim($_POST['competitions_award']) : NULL;
    $competitions_time = !empty($_POST['competitions_time']) ? $_POST['competitions_time'] : NULL;
    $competitions_experience = !empty(trim($_POST['competitions_experience'])) ? trim($_POST['competitions_experience']) : NULL;
    $certifications_name = !empty(trim($_POST['certifications_name'])) ? trim($_POST['certifications_name']) : NULL;
    $certifications_experience = !empty(trim($_POST['certifications_experience'])) ? trim($_POST['certifications_experience']) : NULL;

    // 圖片上傳
    if (isset($_FILES['certificate_image']) && $_FILES['certificate_image']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = 'uploads/';
        $certificate_image_name = basename($_FILES['certificate_image']['name']);
        $certificate_image_path = $upload_dir . $certificate_image_name;

        if (move_uploaded_file($_FILES['certificate_image']['tmp_name'], $certificate_image_path)) {
            $certificate_image = $certificate_image_path;
        } else {
            $certificate_image = NULL;
        }
    } else {
        $certificate_image = NULL;
    }

    // 插入或更新資料
    $merge_query = "
        INSERT INTO achievements (
            account, subject, programming_languages, competitions_name, 
            competitions_award, competitions_time, competitions_experience, 
            certifications_name, certifications_experience, certificate_image
        ) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
            subject = VALUES(subject),
            programming_languages = VALUES(programming_languages),
            competitions_name = VALUES(competitions_name),
            competitions_award = VALUES(competitions_award),
            competitions_time = VALUES(competitions_time),
            competitions_experience = VALUES(competitions_experience),
            certifications_name = VALUES(certifications_name),
            certifications_experience = VALUES(certifications_experience),
            certificate_image = VALUES(certificate_image);
    ";

    $merge_stmt = $conn->prepare($merge_query);
    $merge_stmt->bind_param(
        "ssssssssss",
        $account,
        $subject,
        $programming_languages,
        $competitions_name,
        $competitions_award,
        $competitions_time,
        $competitions_experience,
        $certifications_name,
        $certifications_experience,
        $certificate_image
    );

    if ($merge_stmt->execute()) {
        header("Location: finalachievement.php?msg=資料已成功新增或更新");
        exit;
    } else {
        echo "資料處理失敗，請重試！";
    }
}
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>新增競賽成果</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container px-5 my-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bolder">新增競賽成果</h1>
            <p class="text-muted">請填寫以下欄位以新增您的競賽成果。</p>
        </div>
        <form action="add_competition.php" method="POST" enctype="multipart/form-data">
            
            
            <!-- 競賽成果 -->
            <h2 class="fw-bolder mb-3">競賽成果</h2>
            <div class="mb-4">
                <label for="competitions_name" class="form-label">競賽名稱：</label>
                <input type="text" id="competitions_name" name="competitions_name" class="form-control" placeholder="請輸入競賽名稱">
            </div>
            <div class="mb-4">
                <label for="competitions_award" class="form-label">獎項：</label>
                <input type="text" id="competitions_award" name="competitions_award" class="form-control" placeholder="請輸入獎項">
            </div>
            <div class="mb-4">
                <label for="competitions_time" class="form-label">時間：</label>
                <input type="date" id="competitions_time" name="competitions_time" class="form-control">
            </div>
            <div class="mb-4">
                <label for="competitions_experience" class="form-label">心得：</label>
                <textarea id="competitions_experience" name="competitions_experience" class="form-control" placeholder="請輸入心得"></textarea>
            </div>
            
            
            <button type="submit" class="btn btn-primary">新增</button>
        </form>
    </div>
</body>
</html>
