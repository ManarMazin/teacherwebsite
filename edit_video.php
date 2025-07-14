<?php
session_start();
require 'DENHUS.php'; // اتصال قاعدة البيانات

// تحقق من وجود معرف الفيديو
if (!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    die("معرف الفيديو غير صالح.");
}

$video_id = intval($_GET['id']);
$message = '';

// جلب بيانات الفيديو الحالي
$sql = "SELECT * FROM videos WHERE id = ?";
$stmt = mysqli_prepare($connect, $sql);
mysqli_stmt_bind_param($stmt, 'i', $video_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$video = mysqli_fetch_assoc($result);

if (!$video) {
    die("الفيديو غير موجود.");
}

// معالجة التحديث عند إرسال النموذج
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // استلام القيم الجديدة مع تنظيفها
    $course_id   = intval($_POST['course_id'] ?? 0);
    $course_name = trim($_POST['course_name'] ?? '');
    $category    = trim($_POST['category'] ?? '');
    $level       = trim($_POST['level'] ?? '');
    $title       = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $video_url   = trim($_POST['video_url'] ?? '');

    // إعداد اسماء الملفات (سنحتفظ بالملفات القديمة إذا لم يتم رفع جديد)
    $filename = $video['filename'];
    $extra_file = $video['extra_file'];

    $upload_dir = 'uploads/';

    // رفع ملف الفيديو إذا تم اختيار ملف جديد
    if (isset($_FILES['video_file']) && $_FILES['video_file']['error'] === UPLOAD_ERR_OK) {
        // حذف الملف القديم (اختياري)
        if ($filename && file_exists($upload_dir . $filename)) {
            unlink($upload_dir . $filename);
        }
        $filename = time() . '_' . basename($_FILES['video_file']['name']);
        move_uploaded_file($_FILES['video_file']['tmp_name'], $upload_dir . $filename);
    }

    // رفع الملف الإضافي إذا تم اختيار ملف جديد
    if (isset($_FILES['extra_file']) && $_FILES['extra_file']['error'] === UPLOAD_ERR_OK) {
        if ($extra_file && file_exists($upload_dir . $extra_file)) {
            unlink($upload_dir . $extra_file);
        }
        $extra_file = time() . '_extra_' . basename($_FILES['extra_file']['name']);
        move_uploaded_file($_FILES['extra_file']['tmp_name'], $upload_dir . $extra_file);
    }

    // تحديث البيانات في قاعدة البيانات
    $update_sql = "UPDATE videos SET
        course_id = ?, course_name = ?, category = ?, level = ?, title = ?, description = ?, 
        filename = ?, video_url = ?, extra_file = ?
        WHERE id = ?";
    $update_stmt = mysqli_prepare($connect, $update_sql);
    mysqli_stmt_bind_param(
        $update_stmt, 
        'issssssssi',
        $course_id, $course_name, $category, $level, $title, $description,
        $filename, $video_url, $extra_file, $video_id
    );
    if (mysqli_stmt_execute($update_stmt)) {
        $message = "✅ تم تحديث بيانات الفيديو بنجاح.";
        // إعادة جلب البيانات بعد التحديث
        $stmt = mysqli_prepare($connect, "SELECT * FROM videos WHERE id = ?");
        mysqli_stmt_bind_param($stmt, 'i', $video_id);
        mysqli_stmt_execute($stmt);
        $res = mysqli_stmt_get_result($stmt);
        $video = mysqli_fetch_assoc($res);
    } else {
        $message = "❌ خطأ أثناء تحديث الفيديو: " . mysqli_error($connect);
    }
}

?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تعديل الفيديو</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            direction: rtl;
            background: #f0f8ff;
            padding: 30px;
        }
        .container {
            max-width: 600px;
            background: #fff;
            padding: 20px 30px;
            margin: auto;
            border-radius: 12px;
            box-shadow: 0 0 15px rgba(0,0,0,0.1);
        }
        h2 {
            color: #03a9f4;
            text-align: center;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input[type="text"], input[type="number"], textarea, input[type="url"], input[type="file"] {
            width: 100%;
            padding: 8px;
            margin-top: 5px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        textarea {
            height: 100px;
        }
        .btn-submit {
            margin-top: 20px;
            background: #03a9f4;
            color: white;
            padding: 12px;
            border: none;
            width: 100%;
            font-size: 16px;
            border-radius: 6px;
            cursor: pointer;
        }
        .btn-submit:hover {
            background: #0288d1;
        }
        .message {
            margin-top: 15px;
            text-align: center;
            color: green;
            font-weight: bold;
        }
        .back-link {
            margin-top: 15px;
            display: block;
            text-align: center;
            color: #03a9f4;
            text-decoration: none;
            font-weight: bold;
        }
        .back-link:hover {
            text-decoration: underline;
        }

        
    </style>
    
</head>
<body>


<div class="container">
    <h2>تعديل بيانات الفيديو</h2>


    <?php if ($message): ?>
        <p class="message"><?= htmlspecialchars($message) ?></p>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>رقم المحاضرة (Course ID):</label>
        <input type="number" name="course_id" value="<?= htmlspecialchars($video['course_id']) ?>" required>

        <label>اسم المادة:</label>
        <input type="text" name="course_name" value="<?= htmlspecialchars($video['course_name']) ?>" required>

        <label>التصنيف:</label>
        <input type="text" name="category" value="<?= htmlspecialchars($video['category']) ?>" required>

        <label>المستوى / المرحلة:</label>
        <input type="text" name="level" value="<?= htmlspecialchars($video['level']) ?>" required>

        <label>عنوان الفيديو:</label>
        <input type="text" name="title" value="<?= htmlspecialchars($video['title']) ?>" required>

        <label>وصف الفيديو:</label>
        <textarea name="description" required><?= htmlspecialchars($video['description']) ?></textarea>

        <label>رابط الفيديو (اختياري):</label>
        <input type="url" name="video_url" value="<?= htmlspecialchars($video['video_url']) ?>">

        <label>ملف الفيديو الحالي: <?= htmlspecialchars($video['filename']) ?></label>
        <label>تغيير ملف الفيديو (اختياري):</label>
        <input type="file" name="video_file" accept="video/*">

        <label>الملف الإضافي الحالي: <?= htmlspecialchars($video['extra_file']) ?></label>
        <label>تغيير الملف الإضافي (اختياري):</label>
        <input type="file" name="extra_file" accept=".pdf,.doc,.docx,.xls,.xlsx">

        <button type="submit" class="btn-submit">تحديث الفيديو</button>
    </form>

    <a href="view_all_videos.php" class="back-link">← العودة لصفحة الفيديوهات</a>
</div>

</body>
</html>
