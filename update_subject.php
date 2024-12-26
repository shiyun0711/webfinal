<?php
require_once "finaldb.php";
session_start();

// 確認使用者是否已登入
if (!isset($_SESSION["account"])) {
    header("Location: finallogin.php?msg=\u8acb\u5148\u767b\u5165");
    exit;
}

// 初始化資料庫連線
$conn = new mysqli("localhost", "root", "", "final");
if ($conn->connect_error) {
    die("資料庫連線失敗：" . $conn->connect_error);
}

// 初始化變數
$subject = "";
$programming_languages = "";
$competitions = [];
$certifications = [];

// 查詢單一欄位（擅長科目、程式語言）
$account = $_SESSION["account"];
$query_single = "SELECT subject, programming_languages FROM achievements WHERE account = ?";
$stmt_single = $conn->prepare($query_single);
$stmt_single->bind_param("s", $account);
$stmt_single->execute();
$result_single = $stmt_single->get_result();

if ($result_single->num_rows > 0) {
    $row_single = $result_single->fetch_assoc();
    $subject = $row_single['subject'] ?: "";
    $programming_languages = $row_single['programming_languages'] ?: "";
} else {
    // 若資料不存在，則新增資料
    $insert_query = "INSERT INTO achievements (account, subject, programming_languages) VALUES (?, ?, ?)";
    $stmt_insert = $conn->prepare($insert_query);
    $stmt_insert->bind_param("sss", $account, $subject, $programming_languages);
    $stmt_insert->execute();
    $stmt_insert->close();
}
$stmt_single->close();


// 查詢多筆競賽和證書資料
$query_multi = "SELECT * FROM achievements WHERE account = ?";
$stmt_multi = $conn->prepare($query_multi);
$stmt_multi->bind_param("s", $account);
$stmt_multi->execute();
$result_multi = $stmt_multi->get_result();

while ($row_multi = $result_multi->fetch_assoc()) {
    // 檢查競賽成果是否所有欄位為空
    if (!empty($row_multi['competitions_name']) || !empty($row_multi['competitions_award']) || !empty($row_multi['competitions_time']) || !empty($row_multi['competitions_experience'])) {
        $competitions[] = [
            'id' => $row_multi['id'],
            'competitions_name' => $row_multi['competitions_name'],
            'competitions_award' => $row_multi['competitions_award'],
            'competitions_time' => $row_multi['competitions_time'],
            'competitions_experience' => $row_multi['competitions_experience']
        ];
    }

    // 檢查證書證照是否所有欄位為空
    if (!empty($row_multi['certifications_name']) || !empty($row_multi['certifications_experience']) || !empty($row_multi['certificate_image'])) {
        $certifications[] = [
            'id' => $row_multi['id'],
            'certifications_name' => $row_multi['certifications_name'],
            'certifications_experience' => $row_multi['certifications_experience'],
            'certificate_image' => $row_multi['certificate_image']
        ];
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // 更新擅長科目與程式語言
    if (!empty($_POST['subject']) || !empty($_POST['programming_languages'])) {
        $new_subject = $_POST['subject'];
        $new_programming_languages = $_POST['programming_languages'];
        $query_update_main = "UPDATE achievements SET subject = ?, programming_languages = ? WHERE account = ?";
        $stmt_update_main = $conn->prepare($query_update_main);
        $stmt_update_main->bind_param("sss", $new_subject, $new_programming_languages, $account);
        $stmt_update_main->execute();
        $stmt_update_main->close();
    }

    // 更新競賽成果
    if (!empty($_POST['competitions_name'])) {
        foreach ($_POST['competitions_name'] as $id => $name) {
            $award = $_POST['competitions_award'][$id];
            $time = $_POST['competitions_time'][$id];
            $experience = $_POST['competitions_experience'][$id];
            $query_update_competitions = "UPDATE achievements SET competitions_name = ?, competitions_award = ?, competitions_time = ?, competitions_experience = ? WHERE id = ?";
            $stmt_update = $conn->prepare($query_update_competitions);
            $stmt_update->bind_param("ssssi", $name, $award, $time, $experience, $id);
            $stmt_update->execute();
            $stmt_update->close();
        }
    }

    // 更新證書證照
    if (!empty($_POST['certifications_name'])) {
        foreach ($_POST['certifications_name'] as $id => $name) {
            $experience = $_POST['certifications_experience'][$id];
            // 檢查是否上傳新圖片
            if (!empty($_FILES['certificate_image']['name'][$id])) {
                $image_path = "uploads/" . basename($_FILES['certificate_image']['name'][$id]);
                move_uploaded_file($_FILES['certificate_image']['tmp_name'][$id], $image_path);
                $query_update_certifications = "UPDATE achievements SET certifications_name = ?, certifications_experience = ?, certificate_image = ? WHERE id = ?";
                $stmt_update = $conn->prepare($query_update_certifications);
                $stmt_update->bind_param("sssi", $name, $experience, $image_path, $id);
            } else {
                $query_update_certifications = "UPDATE achievements SET certifications_name = ?, certifications_experience = ? WHERE id = ?";
                $stmt_update = $conn->prepare($query_update_certifications);
                $stmt_update->bind_param("ssi", $name, $experience, $id);
            }
            $stmt_update->execute();
            $stmt_update->close();
        }
    }

    // 刪除競賽成果
    if (!empty($_POST['delete_competitions'])) {
        $delete_competitions = $_POST['delete_competitions'];
        $placeholders = implode(',', array_fill(0, count($delete_competitions), '?'));
        $query_delete_competitions = "DELETE FROM achievements WHERE id IN ($placeholders)";
        $stmt_delete = $conn->prepare($query_delete_competitions);
        $stmt_delete->bind_param(str_repeat('i', count($delete_competitions)), ...$delete_competitions);
        $stmt_delete->execute();
        $stmt_delete->close();
    }

    // 刪除證書證照
    if (!empty($_POST['delete_certifications'])) {
        $delete_certifications = $_POST['delete_certifications'];
        $placeholders = implode(',', array_fill(0, count($delete_certifications), '?'));
        $query_delete_certifications = "DELETE FROM achievements WHERE id IN ($placeholders)";
        $stmt_delete = $conn->prepare($query_delete_certifications);
        $stmt_delete->bind_param(str_repeat('i', count($delete_certifications)), ...$delete_certifications);
        $stmt_delete->execute();
        $stmt_delete->close();
    }

    // 更新成功後跳轉回成就頁面
    header("Location: finalachievement.php?msg=更新成功");
    exit;
}


$stmt_multi->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="zh-TW">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>更新學習成果</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container px-5 my-5">
        <div class="text-center mb-5">
            <h1 class="display-5 fw-bolder">更新學習成果</h1>
            <p class="text-muted">您可以選填以下欄位來更新您的學習成果。</p>
        </div>
        <form action="" method="POST" enctype="multipart/form-data">
            <!-- 擅長科目 -->
            <div class="mb-4">
                <label for="subject" class="form-label">擅長科目：</label>
                <input type="text" id="subject" name="subject" class="form-control" value="<?= htmlspecialchars($subject) ?>">
            </div>

            <!-- 程式語言 -->
            <div class="mb-4">
                <label for="programming_languages" class="form-label">程式語言：</label>
                <input type="text" id="programming_languages" name="programming_languages" class="form-control" value="<?= htmlspecialchars($programming_languages) ?>">
            </div>

            <!-- 競賽成果 -->
            <h2 class="fw-bolder mb-3">競賽成果</h2>
            <?php foreach ($competitions as $competition): ?>
                <div class="mb-4">
                    <label class="form-label">競賽名稱：</label>
                    <input type="text" name="competitions_name[<?= $competition['id'] ?>]" class="form-control" value="<?= htmlspecialchars($competition['competitions_name']) ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label">獎項：</label>
                    <input type="text" name="competitions_award[<?= $competition['id'] ?>]" class="form-control" value="<?= htmlspecialchars($competition['competitions_award']) ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label">時間：</label>
                    <input type="date" name="competitions_time[<?= $competition['id'] ?>]" class="form-control" value="<?= htmlspecialchars($competition['competitions_time']) ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label">心得：</label>
                    <textarea name="competitions_experience[<?= $competition['id'] ?>]" class="form-control"><?= htmlspecialchars($competition['competitions_experience']) ?></textarea>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="delete_competitions[]" value="<?= $competition['id'] ?>" id="delete_competition_<?= $competition['id'] ?>">
                    <label class="form-check-label" for="delete_competition_<?= $competition['id'] ?>">刪除此競賽資料</label>
                </div>
            <?php endforeach; ?>

            <!-- 證書證照 -->
            <h2 class="fw-bolder mb-3">證書證照</h2>
            <?php foreach ($certifications as $certification): ?>
                <div class="mb-4">
                    <label class="form-label">證書名稱：</label>
                    <input type="text" name="certifications_name[<?= $certification['id'] ?>]" class="form-control" value="<?= htmlspecialchars($certification['certifications_name']) ?>">
                </div>
                <div class="mb-4">
                    <label class="form-label">心得：</label>
                    <textarea name="certifications_experience[<?= $certification['id'] ?>]" class="form-control"><?= htmlspecialchars($certification['certifications_experience']) ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">上傳新圖片：</label>
                    <input type="file" name="certificate_image[<?= $certification['id'] ?>]" class="form-control">
                    <?php if (!empty($certification['certificate_image'])): ?>
                        <img src="<?= htmlspecialchars($certification['certificate_image']) ?>" alt="證書圖片" style="width: 120px; height: 160px;" class="mt-3">
                    <?php endif; ?>
                </div>
                <div class="form-check mb-4">
                    <input class="form-check-input" type="checkbox" name="delete_certifications[]" value="<?= $certification['id'] ?>" id="delete_certification_<?= $certification['id'] ?>">
                    <label class="form-check-label" for="delete_certification_<?= $certification['id'] ?>">刪除此證書資料</label>
                </div>
            <?php endforeach; ?>

            <button type="submit" class="btn btn-primary">提交更新</button>
        </form>
    </div>
</body>
</html>
