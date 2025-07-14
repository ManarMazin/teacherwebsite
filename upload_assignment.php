<?php
session_start();
require 'DENHUS.php';

$message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $level = trim($_POST['level'] ?? '');
    $course_id = intval($_POST['course_id'] ?? 0);

    $assignment_file = $_FILES['assignment_file'] ?? null;
    $question_image = $_FILES['question_image'] ?? null;

    $file_path = null;
    $image_path = null;

    // إنشاء مجلد التخزين إن لم يكن موجوداً
    $upload_dir = 'assignments/';
    if (!is_dir($upload_dir)) {
        mkdir($upload_dir, 0777, true);
    }

    // رفع ملف الواجب (اختياري)
    if ($assignment_file && $assignment_file['error'] === UPLOAD_ERR_OK) {
        $file_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($assignment_file['name']));
        $file_path = $upload_dir . $file_name;
        move_uploaded_file($assignment_file['tmp_name'], $file_path);
    }

    // رفع صورة الأسئلة (اختياري)
    if ($question_image && $question_image['error'] === UPLOAD_ERR_OK) {
        $image_name = time() . '_' . preg_replace('/[^a-zA-Z0-9_\.-]/', '_', basename($question_image['name']));
        $image_path = $upload_dir . $image_name;
        move_uploaded_file($question_image['tmp_name'], $image_path);
    }

    // إدخال البيانات في قاعدة البيانات
    $stmt = $connect->prepare("INSERT INTO assignments (title, description, file_path, image_path, level, course_id) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("sssssi", $title, $description, $file_path, $image_path, $level, $course_id);

    if ($stmt->execute()) {
        $message = "✅ تم رفع الواجب بنجاح.";
    } else {
        $message = "❌ حدث خطأ أثناء حفظ البيانات: " . $stmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>رفع واجب</title>
    <style>
        body {
            background-color: #f5f5f5;
            font-family: 'Arial', sans-serif;
            direction: rtl;
            text-align: right;
            padding: 40px;
        }
        .form-container {
            background-color: #fff;
            border-radius: 12px;
            padding: 30px;
            max-width: 600px;
            margin: auto;
            box-shadow: 0 0 10px #ccc;
        }
        label {
            display: block;
            margin: 15px 0 5px;
        }
        input, textarea, select {
            width: 100%;
            padding: 10px;
            border-radius: 6px;
            border: 1px solid #ccc;
            box-sizing: border-box;
        }
        button {
    margin-top: 20px;
    background-color: #00bcd4;
    color: white;
    border: none;
    padding: 15px 0;
    border-radius: 6px;
    cursor: pointer;
    font-weight: bold;
    width: 80%; /* اجعله أعرض */
    display: block;
    margin-left: auto;
    margin-right: auto;
}

button:hover {
    background-color: #0288d1;
}

.back-btn {
    display: block;
    background-color: #999;
    color: white;
    padding: 15px 0;
    border-radius: 6px;
    text-decoration: none;
    margin-top: 15px;
    cursor: pointer;
    width: 80%; /* اجعله أعرض */
    margin-left: auto;
    margin-right: auto;
    text-align: center;
}

.back-btn:hover {
    background-color: #666;
}

        .message {
            margin-top: 15px;
            font-weight: bold;
            color: green;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>📘 رفع واجب جديد</h2>

    <?php if ($message): ?>
        <div class="message"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label for="title">عنوان الواجب:</label>
        <input type="text" name="title" id="title" required>

        <label for="description">وصف الواجب:</label>
        <textarea name="description" id="description" rows="4" required></textarea>

        <label for="level">الصف الدراسي:</label>
        <select name="level" id="level" required>
            <option value="">اختر الصف</option>
            <option value="الثالث متوسط">الثالث متوسط</option>
            <option value="السادس اعدادي">السادس اعدادي</option>
        </select>

        <label for="course_id">رقم الدورة:</label>
        <input type="number" name="course_id" id="course_id" required>

        <label for="assignment_file">📎 ملف الواجب (PDF أو Word) (اختياري):</label>
        <input type="file" name="assignment_file" accept=".pdf,.doc,.docx">

        <label for="question_image">🖼️ صورة الأسئلة (اختياري):</label>
        <input type="file" name="question_image" accept=".jpg,.jpeg,.png">

        <button type="submit">📤 رفع الواجب</button>
    </form>

    <a href="admin.php" class="back-btn">🔙 رجوع</a>
</div>

</body>
</html>

