<?php
require_once "finaldb.php";
session_start();

// 確認使用者是否已登入
if (!isset($_SESSION["account"])) {
    header("Location: finallogin.php?msg=請先登入");
    exit;
}

// 初始化資料庫連線
$conn = new mysqli("localhost", "root", "", "final");
if ($conn->connect_error) {
    die("資料庫連線失敗：" . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $account = $_SESSION["account"];

    // 處理擅長科目與程式語言
    $subject = !empty($_POST['subject']) ? $_POST['subject'] : NULL;
    $programming_languages = !empty($_POST['programming_languages']) ? $_POST['programming_languages'] : NULL;

    // 更新單一欄位
    $query_single = "UPDATE achievements SET subject = ?, programming_languages = ? WHERE account = ?";
    $stmt_single = $conn->prepare($query_single);
    $stmt_single->bind_param("sss", $subject, $programming_languages, $account);
    $stmt_single->execute();

    // 處理競賽成果與證書證照
    foreach ($_POST['competitions_name'] as $id => $competitions_name) {
        $competitions_award = $_POST['competitions_award'][$id] ?? NULL;
        $competitions_time = $_POST['competitions_time'][$id] ?? NULL;
        $competitions_experience = $_POST['competitions_experience'][$id] ?? NULL;

        $query_competition = "UPDATE achievements SET 
                                competitions_name = ?, 
                                competitions_award = ?, 
                                competitions_time = ?, 
                                competitions_experience = ? 
                              WHERE id = ? AND account = ?";
        $stmt_competition = $conn->prepare($query_competition);
        $stmt_competition->bind_param(
            "ssssss",
            $competitions_name,
            $competitions_award,
            $competitions_time,
            $competitions_experience,
            $id,
            $account
        );
        $stmt_competition->execute();
    }

    foreach ($_POST['certifications_name'] as $id => $certifications_name) {
        $certifications_experience = $_POST['certifications_experience'][$id] ?? NULL;

        // 圖片路徑處理
        $certificate_image_path = NULL;
        if (isset($_FILES['certificate_image']['name'][$id]) && $_FILES['certificate_image']['error'][$id] === UPLOAD_ERR_OK) {
            $upload_dir = 'uploads/';
            if (!is_dir($upload_dir)) {
                mkdir($upload_dir, 0777, true); // 確保目錄存在
            }

            $file_name = basename($_FILES['certificate_image']['name'][$id]);
            $target_file = $upload_dir . uniqid() . "_" . $file_name;

            if (move_uploaded_file($_FILES['certificate_image']['tmp_name'][$id], $target_file)) {
                $certificate_image_path = $target_file;
            }
        }

        $query_certification = "UPDATE achievements SET 
                                    certifications_name = ?, 
                                    certifications_experience = ?, 
                                    certificate_image = IFNULL(?, certificate_image) 
                                WHERE id = ? AND account = ?";
        $stmt_certification = $conn->prepare($query_certification);
        $stmt_certification->bind_param(
            "sssss",
            $certifications_name,
            $certifications_experience,
            $certificate_image_path,
            $id,
            $account
        );
        $stmt_certification->execute();
    }

    // 更新完成後跳轉到 finalachievement.php
    header("Location: finalachievement.php?msg=資料更新成功");
    exit;
}

$conn->close();
