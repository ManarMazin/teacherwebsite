<?php
session_start();
require 'DENHUS.php';

$error = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST["st_name"]);
    $token = trim($_POST["st_token"]);

    if (!empty($name) && !empty($token)) {
        $stmt = $connect->prepare("SELECT * FROM student WHERE st_name = ? AND st_token = ?");
        if ($stmt) {
            $stmt->bind_param("ss", $name, $token);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result && $result->num_rows === 1) {
                $user = $result->fetch_assoc();
                $_SESSION['st_name'] = $user['st_name'];
                $_SESSION['st_level'] = $user['st_level']; // مهم تطابق الاسم مع show_hw.php

                header("Location: st_dashboard.php");
                exit();
            } else {
                $error = "بيانات الدخول غير صحيحة.";
            }
            $stmt->close();
        } else {
            $error = "خطأ في تحضير الاستعلام.";
        }
    } else {
        $error = "يرجى تعبئة الحقول.";
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تسجيل دخول الطالب</title>
  <style>
    body { background-color: #f0f0f0; font-family: 'Cairo', sans-serif; }
    .login-box { width: 400px; margin: 80px auto; background: white; padding: 30px; border-radius: 12px; box-shadow: 0 0 12px rgba(0,0,0,0.1);}
    h2 { text-align: center; color: #00bcd4; margin-bottom: 20px;}
    input { width: 100%; padding: 10px; margin-top: 12px; border: 1px solid #ccc; border-radius: 6px;}
    button { width: 100%; background-color: #00bcd4; color: white; padding: 12px; margin-top: 20px; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;}
    button:hover { background-color: #0097a7;}
    .error { color: red; text-align: center; margin-top: 10px;}
  </style>
</head>
<body>

  <div class="login-box">
    <h2>تسجيل دخول الطالب</h2>

    <?php if ($error): ?>
      <div class="error"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" action="">
      <input type="text" name="st_name" placeholder="اسم الطالب" required>
      <input type="text" name="st_token" placeholder="رمز الدخول" required>
      <button type="submit">دخول</button>
    </form>
  </div>

</body>
</html>
