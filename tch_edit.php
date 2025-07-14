<?php
require "manar.php";
global $connect;
mysqli_set_charset($connect, "utf8");

$token = $_GET['T'] ?? '';
$success_message = '';
$error_message = '';

if (!$token) {
    echo "<p>رمز الأستاذ غير موجود.</p>";
    exit;
}

$query = "SELECT * FROM teachers WHERE tea_token = '$token'";
$result = mysqli_query($connect, $query);
$teacher = mysqli_fetch_assoc($result);

if (!$teacher) {
    echo "<p>الأستاذ غير موجود.</p>";
    exit;
}

if (isset($_POST['update'])) {
    $name = mysqli_real_escape_string($connect, $_POST['name']);
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $cat = mysqli_real_escape_string($connect, $_POST['cat']);
    
    // الحفاظ على السيرة الذاتية القديمة إذا لم يتم رفع ملف جديد
    $cvFileName = $teacher['tea_cv'];

    // إذا تم رفع ملف جديد
    if (!empty($_FILES['cv']['name'])) {
        // التحقق من وجود المجلد
        $uploadDir = 'uploads/cv/';
        if (!file_exists($uploadDir)) {
            mkdir($uploadDir, 0777, true);  // إنشاء المجلد مع الأذونات المناسبة
        }
        
        $fileName = basename($_FILES['cv']['name']);
        $ext = pathinfo($fileName, PATHINFO_EXTENSION);
        $allowed = ['pdf', 'doc', 'docx'];

        // التحقق من نوع الملف
        if (in_array(strtolower($ext), $allowed)) {
            // التحقق من حجم الملف (مثلاً أقل من 5 ميجابايت)
            if ($_FILES['cv']['size'] < 5000000) {
                $newFileName = uniqid('cv_') . '.' . $ext;
                $filePath = $uploadDir . $newFileName;

                // محاولة رفع الملف
                if (move_uploaded_file($_FILES['cv']['tmp_name'], $filePath)) {
                    $cvFileName = $newFileName; // تغيير اسم الملف إذا تم رفعه بنجاح
                } else {
                    $error_message = "❌ فشل في رفع السيرة الذاتية.";
                }
            } else {
                $error_message = "❌ حجم الملف كبير جدًا. الحد الأقصى هو 5 ميجابايت.";
            }
        } else {
            $error_message = "❌ فقط الملفات بصيغة PDF أو Word مسموحة.";
        }
    }

    if (empty($error_message)) {
        // تحديث بيانات الأستاذ في قاعدة البيانات
        $updateQuery = "
            UPDATE teachers 
            SET tea_name = '$name', tea_email = '$email', tea_cat = '$cat', tea_cv = '$cvFileName' 
            WHERE tea_token = '$token'
        ";

        if (mysqli_query($connect, $updateQuery)) {
            $success_message = "✅ تم تعديل البيانات بنجاح.";
            // تحديث معلومات الأستاذ في المصفوفة لتظهر في الصفحة بعد التعديل
            $teacher = ['tea_name' => $name, 'tea_email' => $email, 'tea_cat' => $cat, 'tea_cv' => $cvFileName];
        } else {
            $error_message = "❌ حدث خطأ أثناء التعديل.";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تعديل بيانات الأستاذ</title>
    <style>
        body {
            font-family: 'Tahoma', sans-serif;
            background: linear-gradient(to right, #00b4db, #0083b0);
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 500px;
            margin: 70px auto;
            background: #fff;
            padding: 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            color: #0083b0;
        }
        label {
            display: block;
            margin-top: 15px;
            font-weight: bold;
        }
        input, select {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        button {
            margin-top: 20px;
            background: #0083b0;
            color: white;
            border: none;
            padding: 12px;
            width: 100%;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #006f98;
        }
        .message {
            margin-top: 10px;
            text-align: center;
            font-weight: bold;
        }
        .success { color: green; }
        .error { color: red; }
        .back-link {
            display: block;
            margin-top: 20px;
            text-align: center;
            color: #007aa6;
            text-decoration: none;
        }
        .back-link:hover {
            text-decoration: underline;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>تعديل بيانات الأستاذ</h2>

    <?php if ($success_message): ?>
        <div class="message success"><?= $success_message ?></div>
    <?php elseif ($error_message): ?>
        <div class="message error"><?= $error_message ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <label>اسم الأستاذ:</label>
        <input type="text" name="name" value="<?= htmlspecialchars($teacher['tea_name']) ?>" required>

        <label>الإيميل:</label>
        <input type="email" name="email" value="<?= htmlspecialchars($teacher['tea_email']) ?>" required>

        <label>الاختصاص:</label>
        <input type="text" name="cat" value="<?= htmlspecialchars($teacher['tea_cat']) ?>" required>

        <label>السيرة الذاتية (PDF أو Word):</label>
        <input type="file" name="cv" accept=".pdf,.doc,.docx">

        <?php if (!empty($teacher['tea_cv'])): ?>
            <p>📄 <a href="uploads/cv/<?= htmlspecialchars($teacher['tea_cv']) ?>" target="_blank">عرض السيرة الذاتية الحالية</a></p>
        <?php endif; ?>

        <button type="submit" name="update">تحديث البيانات</button>
    </form>

    <a href="alltech.php" class="back-link">⬅ العودة</a>
</div>
</body>
</html>
