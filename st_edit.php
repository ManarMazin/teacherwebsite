<?php
require 'DENHUS.php';
mysqli_set_charset($connect, 'utf8');

if (!isset($_GET['token'])) {
    echo "رمز الدخول غير موجود.";
    exit;
}

$token = $_GET['token'];
$result = mysqli_query($connect, "SELECT * FROM student WHERE st_token = '$token'");

if (!$result || mysqli_num_rows($result) == 0) {
    echo "المستخدم غير موجود.";
    exit;
}

$student = mysqli_fetch_assoc($result);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['st_name'];
    $phone = $_POST['st_phone'];
    $level = $_POST['st_level'];

    $update = mysqli_query($connect, "UPDATE student SET st_name='$name', st_phone='$phone', st_level='$level' WHERE st_token='$token'");
    
    if ($update) {
        header("Location: online_st.php");
        exit;
    } else {
        echo "حدث خطأ أثناء التحديث.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>تعديل بيانات طالب</title>
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            direction: rtl;
            background-color: #f0f0f0;
            padding: 40px;
        }
        form {
            background: white;
            padding: 30px;
            border-radius: 10px;
            width: 400px;
            margin: auto;
            box-shadow: 0 2px 10px rgba(0, 188, 212, 0.2);
        }
        input[type="text"], select {
            width: 100%;
            padding: 12px;
            margin: 8px 0 20px;
            border: 1px solid #ccc;
            border-radius: 6px;
        }
        input[type="submit"] {
            background-color: #00bcd4;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            cursor: pointer;
            width: 100%;
        }
    </style>
</head>
<body>

<h2 style="text-align:center; color:#00bcd4;">تعديل بيانات الطالب</h2>

<form method="post">
    <label>الاسم:</label>
    <input type="text" name="st_name" value="<?= htmlspecialchars($student['st_name']) ?>" required>

    <label>الصف:</label>
    <input type="text" name="st_level" value="<?= htmlspecialchars($student['st_level']) ?>" required>

    <label>رقم الهاتف:</label>
    <input type="text" name="st_phone" value="<?= htmlspecialchars($student['st_phone']) ?>" required>

    <input type="submit" value="تحديث">
</form>

</body>
</html>
