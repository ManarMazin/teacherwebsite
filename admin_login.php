<?php
session_start();
require 'DENHUS.php';

// التحقق مما إذا كان المستخدم قد سجل الدخول بالفعل
if (isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit();
}

$error_message = '';
$success_message = '';

if (isset($_POST['login'])) {
    $email = mysqli_real_escape_string($connect, $_POST['email']);
    $password = $_POST['password'];

    // استعلام للتحقق من البريد الإلكتروني
    $query = "SELECT * FROM teachers WHERE tea_email = '$email'";
    $result = mysqli_query($connect, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $admin = mysqli_fetch_assoc($result);
        
        // التحقق من كلمة المرور باستخدام password_verify
        if (password_verify($password, $admin['tea_pass'])) {
            session_regenerate_id(true); // تجديد معرف الجلسة
            $_SESSION['admin_id'] = $admin['tea_id'];
            $_SESSION['admin_email'] = $admin['tea_email'];

            // إعادة التوجيه إلى صفحة الإدارة
            header("Location: admin.php");
            exit();
        } else {
            $error_message = "❌ البريد الإلكتروني أو كلمة المرور غير صحيحة!";
        }
    } else {
        $error_message = "❌ البريد الإلكتروني أو كلمة المرور غير صحيحة!";
    }
}

// عملية إنشاء حساب جديد
if (isset($_POST['create_account'])) {
    $email = mysqli_real_escape_string($connect, $_POST['new_email']);
    $password = $_POST['new_password'];
    $confirm = $_POST['confirm_password'];
    $name = mysqli_real_escape_string($connect, $_POST['new_name']);

    // التحقق من تطابق كلمة المرور مع تأكيد كلمة المرور
    if ($password !== $confirm) {
        $error_message = "❌ كلمة المرور غير متطابقة!";
    } else {
        // التحقق من وجود البريد الإلكتروني في قاعدة البيانات
        $check = mysqli_query($connect, "SELECT * FROM teachers WHERE tea_email = '$email'");
        if (mysqli_num_rows($check) > 0) {
            $error_message = "❌ البريد مسجل مسبقًا!";
        } else {
            // تشفير كلمة المرور باستخدام password_hash
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $token = bin2hex(random_bytes(16));

            // إدخال الحساب الجديد في قاعدة البيانات
            $insert = mysqli_query($connect, "INSERT INTO teachers (tea_name, tea_email, tea_pass) VALUES ('$name', '$email', '$hashed_password')");
            
            if ($insert) {
                $success_message = "✅ تم إنشاء الحساب بنجاح!";
            } else {
                $error_message = "❌ حدث خطأ أثناء إنشاء الحساب.";
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تسجيل الدخول / إنشاء حساب</title>
    <style>
        body {
            font-family: 'Tahoma', sans-serif;
            background: linear-gradient(to right, #00b4db, #0083b0);
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 420px;
            margin: 80px auto;
            background: #fff;
            padding: 25px 30px;
            border-radius: 12px;
            box-shadow: 0 0 20px rgba(0,0,0,0.1);
        }
        h2 {
            text-align: center;
            margin-bottom: 25px;
            color: #0083b0;
        }
        form {
            display: none;
        }
        form.active {
            display: block;
        }
        label {
            display: block;
            margin-top: 12px;
            color: #333;
        }
        input[type="email"], input[type="password"], input[type="text"] {
            width: 100%;
            padding: 10px;
            margin-top: 6px;
            border-radius: 5px;
            border: 1px solid #ccc;
        }
        button {
            margin-top: 20px;
            width: 100%;
            background: #0083b0;
            color: white;
            padding: 12px;
            border: none;
            border-radius: 6px;
            font-size: 16px;
            cursor: pointer;
        }
        button:hover {
            background: #007099;
        }
        .tabs {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .tab {
            flex: 1;
            text-align: center;
            padding: 10px;
            background: #f1f1f1;
            cursor: pointer;
            border-radius: 8px 8px 0 0;
            font-weight: bold;
        }
        .tab.active {
            background: #fff;
            color: #0083b0;
            border-bottom: 2px solid #fff;
        }
        .message {
            text-align: center;
            margin: 10px 0;
            color: red;
        }
        .success {
            color: green;
        }
    </style>
    <script>
        function showTab(tabName) {
            document.getElementById('login').classList.remove('active');
            document.getElementById('register').classList.remove('active');
            document.getElementById(tabName).classList.add('active');

            document.getElementById('tab-login').classList.remove('active');
            document.getElementById('tab-register').classList.remove('active');
            document.getElementById('tab-' + tabName).classList.add('active');
        }

        // احتفظ بالتبويب المفتوح عند العودة من الرسالة
        window.onload = function () {
            <?php if (!empty($success_message)) { ?>
                showTab('login');
            <?php } elseif (!empty($error_message) && isset($_POST['create_account'])) { ?>
                showTab('register');
            <?php } ?>
        };
    </script>
</head>
<body>

<div class="container">
    <div class="tabs">
        <div id="tab-login" class="tab active" onclick="showTab('login')">تسجيل الدخول</div>
        <div id="tab-register" class="tab" onclick="showTab('register')">إنشاء حساب</div>
    </div>

    <?php if (!empty($error_message)): ?>
        <div class="message"><?php echo $error_message; ?></div>
    <?php endif; ?>
    <?php if (!empty($success_message)): ?>
        <div class="message success"><?php echo $success_message; ?></div>
    <?php endif; ?>

    <!-- تسجيل الدخول -->
    <form id="login" method="POST" class="active">
        <label for="email">البريد الإلكتروني:</label>
        <input type="email" id="email" name="email" required>

        <label for="password">كلمة المرور:</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" name="login">تسجيل الدخول</button>
    </form>

    <!-- إنشاء حساب -->
    <form id="register" method="POST">
        <label for="new_name">اسم المدير:</label>
        <input type="text" id="new_name" name="new_name" required>

        <label for="new_email">البريد الإلكتروني:</label>
        <input type="email" id="new_email" name="new_email" required>

        <label for="new_password">كلمة المرور:</label>
        <input type="password" id="new_password" name="new_password" required>

        <label for="confirm_password">تأكيد كلمة المرور:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>

        <button type="submit" name="create_account">إنشاء الحساب</button>
    </form>
</div>

</body>
</html>