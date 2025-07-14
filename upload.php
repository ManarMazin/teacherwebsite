<?php
session_start();
require 'DENHUS.php';  // ملف الاتصال بقاعدة البيانات

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // استقبال بيانات الفورم
    $course_id       = intval($_POST['course_id'] ?? 0);
    $course_name     = trim($_POST['course_name'] ?? '');
    $category        = trim($_POST['category'] ?? '');
    $level           = trim($_POST['level'] ?? '');
    $title           = trim($_POST['video_title'] ?? '');
    $desc            = trim($_POST['video_description'] ?? '');
    $video_url       = trim($_POST['video_url'] ?? '');
    $file            = $_FILES['video_file'] ?? null;
    $extra           = $_FILES['extra_file'] ?? null;

    // التحقق مما إذا كان course_id موجود مسبقًا
    $check_sql = "SELECT id FROM videos WHERE course_id = ?";
    $check_stmt = mysqli_prepare($connect, $check_sql);
    if ($check_stmt) {
        mysqli_stmt_bind_param($check_stmt, 'i', $course_id);
        mysqli_stmt_execute($check_stmt);
        mysqli_stmt_store_result($check_stmt);

        if (mysqli_stmt_num_rows($check_stmt) > 0) {
            $message = "❌ هذا الرقم (Course ID) مسجل مسبقًا. يرجى استخدام رقم مختلف.";
        } else {
            // رفع الملفات
            $upload_dir      = 'uploads/';
            $filename        = '';
            $extra_filename  = '';

            if ($file && $file['error'] === UPLOAD_ERR_OK) {
                $filename = time() . '_' . basename($file['name']);
                move_uploaded_file($file['tmp_name'], $upload_dir . $filename);
            }
            if ($extra && $extra['error'] === UPLOAD_ERR_OK) {
                $extra_filename = time() . '_extra_' . basename($extra['name']);
                move_uploaded_file($extra['tmp_name'], $upload_dir . $extra_filename);
            }

            // إدخال السجل
            $ins = "INSERT INTO videos 
                (course_id, course_name, category, level, title, description, filename, video_url, extra_file, uploaded_at)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())";
            $istmt = mysqli_prepare($connect, $ins);
            if (!$istmt) {
                die("❌ تحضير الاستعلام فشل: " . mysqli_error($connect));
            }
           mysqli_stmt_bind_param(
    $istmt,
    'iisssssss', // 9 أنواع لـ 9 متغيرات
    $course_id,
    $course_name,
    $category,
    $level,
    $title,
    $desc,
    $filename,
    $video_url,
    $extra_filename
);

            if (mysqli_stmt_execute($istmt)) {
                $message = "✅ تم رفع الفيديو بنجاح!";
            } else {
                $message = "❌ خطأ عند حفظ الفيديو: " . mysqli_error($connect);
            }
        }

        mysqli_stmt_close($check_stmt);
    } else {
        $message = "❌ فشل في التحقق من course_id: " . mysqli_error($connect);
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>رفع فيديو جديد</title>
    <style>
        body { font-family: 'Segoe UI', sans-serif; background: #f0f2f5; direction: rtl; padding: 30px; }
        .container { max-width: 600px; margin: auto; background: #fff; padding: 20px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.1); }
        h2 { text-align: center; color: #3d8fa5ff; }
        label { display: block; margin-top: 15px; font-weight: bold; }
        input[type="number"], input[type="text"], textarea, input[type="file"], input[type="url"] {
            width: 100%; padding: 8px; margin-top: 5px; border: 1px solid #ccc; border-radius: 6px;
        }
        textarea { height: 100px; }
        .btn { margin-top: 20px; width: 100%; background: #3d8fa5ff; color: #fff; padding: 10px; border: none; border-radius: 6px; cursor: pointer; font-size: 16px; }
        .btn:hover { background: #037994ff; }
        .message { margin-top: 15px; text-align: center; color: red; }
    </style>
</head>
<body>
<div class="container">
    <h2>رفع فيديو جديد</h2>
    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>
    <form method="POST" enctype="multipart/form-data">
        <label for="course_id">رقم المحاضرة (Course ID):</label>
        <input type="number" name="course_id" id="course_id" min="1" required>

        <label for="course_name">اسم المادة:</label>
        <input type="text" name="course_name" id="course_name" required>

        <label for="category">التصنيف:</label>
        <input type="text" name="category" id="category" required>

        <label for="level">المستوى / المرحلة:</label>
        <input type="text" name="level" id="level" required>

        <label for="video_title">عنوان الفيديو:</label>
        <input type="text" name="video_title" id="video_title" required>

        <label for="video_description">وصف الفيديو:</label>
        <textarea name="video_description" id="video_description" required></textarea>

        <label for="video_url">رابط الفيديو (اختياري):</label>
        <input type="url" name="video_url" id="video_url">

        <label for="video_file">ملف الفيديو (اختياري):</label>
        <input type="file" name="video_file" id="video_file" accept="video/*">

        <label for="extra_file">ملف إضافي (اختياري):</label>
        <input type="file" name="extra_file" id="extra_file" accept=".pdf,.doc,.docx,.xls,.xlsx">

        <button type="submit" class="btn">رفع الفيديو</button>
        </br></br>
        <button class="btn" type="button" onclick="window.location.href='admin.php'">الرجوع إلى لوحة التحكم</button>
    </form>
</div>
</body>
</html>
