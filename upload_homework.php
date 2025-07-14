<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();
require 'DENHUS.php';

$message = "";
$showResult = false;
$uploadedData = [];

// في بداية الصفحة: إذا بيانات نجاح موجودة في الجلسة، نستخدمها ثم نمسحها
if (isset($_SESSION['upload_success'])) {
    $uploadedData = $_SESSION['upload_success'];
    unset($_SESSION['upload_success']);
    $showResult = true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $student_name = trim($_POST['student_name'] ?? '');
    $homework_title = trim($_POST['homework_title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $level = trim($_POST['level'] ?? '');
    $file = $_FILES['homework_file'] ?? null;

    if (!$student_name || !$homework_title || !$file) {
        $message = "❌ يرجى ملء جميع الحقول المطلوبة وتحميل الملف.";
    } else {
        $upload_dir = 'homeworks/';

        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }

        if ($file['error'] === UPLOAD_ERR_OK) {
            $filename = time() . '_' . basename($file['name']);
            $target_path = $upload_dir . $filename;

            $allowed_extensions = ['pdf', 'doc', 'docx', 'txt', 'zip'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

            if (!in_array($ext, $allowed_extensions)) {
                $message = "❌ نوع الملف غير مدعوم. يرجى رفع ملفات PDF، DOC، DOCX، TXT أو ZIP فقط.";
            } else {
                if (move_uploaded_file($file['tmp_name'], $target_path)) {
                    $insert_sql = "INSERT INTO homeworks (student_name, homework_title, description, filename, uploaded_at, level) 
                                   VALUES (?, ?, ?, ?, NOW(), ?)";
                    $stmt = mysqli_prepare($connect, $insert_sql);

                    if ($stmt) {
                        mysqli_stmt_bind_param($stmt, 'sssss', $student_name, $homework_title, $description, $filename, $level);
                        if (mysqli_stmt_execute($stmt)) {
                            // حفظ البيانات في الجلسة لعرضها بعد إعادة التحميل
                            $_SESSION['upload_success'] = [
                                'student_name' => $student_name,
                                'homework_title' => $homework_title,
                                'description' => $description,
                                'filename' => $filename,
                            ];
                            mysqli_stmt_close($stmt);
                            // إعادة التوجيه للصفحة نفسها لتفادي إعادة إرسال النموذج عند التحديث
                            header("Location: " . $_SERVER['PHP_SELF']);
                            exit();
                        } else {
                            $message = "❌ حدث خطأ أثناء حفظ بيانات الواجب: " . mysqli_error($connect);
                            unlink($target_path);
                        }
                    } else {
                        $message = "❌ خطأ في تحضير الاستعلام.";
                        unlink($target_path);
                    }
                } else {
                    $message = "❌ فشل رفع الملف.";
                }
            }
        } else {
            $message = "❌ حدث خطأ أثناء رفع الملف.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>رفع واجب جديد</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
            background: #f0f8ff;
            padding: 30px;
            margin: 0;
        }
        .container {
            max-width: 550px;
            background: #fff;
            padding: 30px 40px;
            margin: auto;
            border-radius: 12px;
            box-shadow: 0 0 18px rgba(0,0,0,0.1);
        }
        h2 {
            color: #03a9f4;
            text-align: center;
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
            font-size: 1.1em;
        }
        input[type="text"], textarea, input[type="file"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 8px;
            border: 1px solid #ccc;
            box-sizing: border-box;
            font-size: 1em;
            resize: vertical;
        }
        textarea {
            height: 100px;
        }
        button, a.button-link {
            margin-top: 25px;
            background: #03a9f4;
            color: white;
            padding: 14px;
            border: none;
            width: 100%;
            font-size: 1.1em;
            border-radius: 8px;
            cursor: pointer;
            text-align: center;
            text-decoration: none;
            display: inline-block;
            transition: background-color 0.3s ease;
        }
        button:hover, a.button-link:hover {
            background: #0288d1;
        }
        .message {
            margin-top: 15px;
            font-weight: bold;
            text-align: center;
            color: red;
        }
        .success-message {
            color: #2e7d32;
            background: #e8f5e9;
            border: 1px solid #a5d6a7;
            padding: 15px 20px;
            border-radius: 10px;
            margin-top: 25px;
            text-align: center;
            font-size: 1.1em;
        }
        .result-info {
            margin-top: 15px;
            font-size: 1.1em;
            line-height: 1.6;
            color: #333;
        }
        .result-info span {
            font-weight: bold;
            color: #007acc;
        }
        .buttons-container {
            margin-top: 25px;
            display: flex;
            gap: 15px;
            justify-content: center;
            flex-wrap: wrap;
        }
        .buttons-container a.button-link {
            width: auto;
            padding: 12px 20px;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>رفع واجب جديد</h2>

    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <?php if ($showResult): ?>
        <div class="success-message">
            <p>✅ تم رفع الواجب بنجاح!</p>
            <div class="result-info">
                <p><span>اسم الطالب:</span> <?= htmlspecialchars($uploadedData['student_name']) ?></p>
                <p><span>عنوان الواجب:</span> <?= htmlspecialchars($uploadedData['homework_title']) ?></p>
                <p><span>الوصف:</span> <?= nl2br(htmlspecialchars($uploadedData['description'])) ?></p>
                <p><span>اسم الملف:</span> <?= htmlspecialchars($uploadedData['filename']) ?></p>
            </div>
            <div class="buttons-container">
                <a href="upload_homework.php" class="button-link">↩️ رفع واجب جديد</a>
                <a href="st_dashboard.php" class="button-link" style="background:#0097a7;">🏠 العودة للوحة التحكم</a>
            </div>
        </div>
    <?php else: ?>
        <form method="POST" enctype="multipart/form-data" novalidate>
            <label>اسم الطالب:</label>
            <input type="text" name="student_name" required value="<?= isset($_POST['student_name']) ? htmlspecialchars($_POST['student_name']) : '' ?>">

            <label>الصف:</label>
            <input type="text" name="level" value="<?= isset($_POST['level']) ? htmlspecialchars($_POST['level']) : '' ?>">

            <label>عنوان الواجب:</label>
            <input type="text" name="homework_title" required value="<?= isset($_POST['homework_title']) ? htmlspecialchars($_POST['homework_title']) : '' ?>">

            <label>وصف الواجب (اختياري):</label>
            <textarea name="description"><?= isset($_POST['description']) ? htmlspecialchars($_POST['description']) : '' ?></textarea>

            <label>ملف الواجب:</label>
            <input type="file" name="homework_file" accept=".pdf,.doc,.docx,.txt,.zip" required>

            <button type="submit">رفع الواجب</button>
        </form>
    <?php endif; ?>
</div>

</body>
</html>
