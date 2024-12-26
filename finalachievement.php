<?php 
// 載入資料庫連接
require_once "finalheader.php";
require_once 'finaldb.php';

// 確認使用者是否已登入
if (!isset($_SESSION["account"])) {
    header("Location: finallogin.php?msg=請先登入");
    exit;
}

// 從 session 中取得帳號
$account = $_SESSION["account"];

// 建立 MySQLi 連線
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "final";

$conn = new mysqli($servername, $username, $password, $dbname);

// 檢查連線是否成功
if ($conn->connect_error) {
    die("資料庫連線失敗：" . $conn->connect_error);
}

// 查詢單一欄位資料（擅長科目、程式語言）
$query_single = "SELECT subject, programming_languages FROM achievements WHERE account = ?";
$stmt_single = $conn->prepare($query_single);
$stmt_single->bind_param("s", $account);
$stmt_single->execute();
$result_single = $stmt_single->get_result();

if ($result_single->num_rows > 0) {
    $row_single = $result_single->fetch_assoc();
    $subject = $row_single['subject'] ?: "尚未設定擅長科目";
    $programming_languages = $row_single['programming_languages'] ?: "尚未設定程式語言";
} else {
    $subject = "尚未設定擅長科目";
    $programming_languages = "尚未設定程式語言";
}
$stmt_single->close();

// 查詢多筆資料（競賽成果、證書證照）
$query_multi = "SELECT * FROM achievements WHERE account = ?";
$stmt_multi = $conn->prepare($query_multi);
$stmt_multi->bind_param("s", $account);
$stmt_multi->execute();
$result_multi = $stmt_multi->get_result();

$achievements = [];
while ($row_multi = $result_multi->fetch_assoc()) {
    $achievements[] = $row_multi;
}

$stmt_multi->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="zh-TW">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>學習成果</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <div class="container px-5 my-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bolder mb-0"><span class="text-gradient d-inline">學習成果</span></h1>
        </div>

        <!-- 擅長科目 -->
        <section class="mb-5">
            <h2 class="fw-bolder mb-3" style="color: #6f42c1;">擅長科目</h2>
            <p style="font-size: 1.5rem; color: #333;"><?= htmlspecialchars($subject) ?></p>
        </section>

        <!-- 程式語言 -->
        <section class="mb-5">
            <h2 class="fw-bolder mb-3" style="color: #6f42c1;">程式語言</h2>
            <p style="font-size: 1.5rem; color: #333;"><?= htmlspecialchars($programming_languages) ?></p>
        </section>

        <!-- 競賽成果 -->
        <section class="mb-5">
            <h2 class="text-primary fw-bolder mb-3">競賽成果</h2>
            <?php if (!empty($achievements)): ?>
                <?php foreach ($achievements as $achievement): ?>
                    <?php if (!empty($achievement['competitions_name'])): ?>
                        <div class="card shadow border-0 rounded-4 mb-4">
                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($achievement['competitions_name']) ?></h5>
                                <p class="card-text">
                                    獎項：<?= htmlspecialchars($achievement['competitions_award']) ?: '尚未設定獎項' ?><br>
                                    時間：<?= htmlspecialchars($achievement['competitions_time']) ?: '尚未設定時間' ?><br>
                                    心得：<?= htmlspecialchars($achievement['competitions_experience']) ?: '尚未設定心得' ?>
                                </p>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">尚未新增競賽成果。</p>
            <?php endif; ?>
        </section>

        <!-- 證書證照 -->
        <section class="mb-5">
            <h2 class="text-secondary fw-bolder mb-3">證書證照</h2>
            <?php if (!empty($achievements)): ?>
                <?php foreach ($achievements as $achievement): ?>
                    <?php if (!empty($achievement['certifications_name'])): ?>
                        <div class="card shadow border-0 rounded-4 mb-5">
                            <div class="card-body">
                                <div class="row align-items-center gx-5">
                                    <div class="col text-center text-lg-start mb-4 mb-lg-0">
                                        <div class="bg-light p-4 rounded-4">
                                            <div class="text-secondary fw-bolder mb-2"><?= htmlspecialchars($achievement['certifications_name']) ?></div>
                                            <div class="mb-2"><?= htmlspecialchars($achievement['certifications_experience']) ?: '尚未設定心得' ?></div>
                                        </div>
                                    </div>
                                    <?php if (!empty($achievement['certificate_image'])): ?>
                                        <div class="col-lg-8">
                                            <img src="<?= htmlspecialchars($achievement['certificate_image']) ?>" alt="證書圖片" class="img-fluid rounded-4">
                                        </div>
                                    <?php else: ?>
                                        <div class="col-lg-8">
                                            <p class="text-muted">尚無證書圖片</p>
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                <?php endforeach; ?>
            <?php else: ?>
                <p class="text-muted">尚未新增證書證照。</p>
            <?php endif; ?>
        </section>

        <!-- 操作按鈕 -->
        <div class="d-flex justify-content-center align-items-center mb-5">
            <a class="btn px-4 py-3 mx-2" 
               style="background-color: #6f42c1; border-color: #6f42c1; color: white;" 
               href="update_subject.php">
                編輯學習成果
            </a>
            <a class="btn btn-primary px-4 py-3 mx-2" href="add_competition.php">
                新增競賽成果
            </a>
            <a class="btn btn-secondary px-4 py-3 mx-2" href="add_certification.php">
                新增證書證照
            </a>
        </div>
    </div>
    <div style="height: 3rem;"></div>
</body>

</html>
