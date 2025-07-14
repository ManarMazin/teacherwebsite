<?php
session_start();
require 'DENHUS.php';

$success = $error = "";

function generateToken($length = 16) {
    return bin2hex(random_bytes($length));
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name  = trim($_POST["st_name"]);
    $age   = intval($_POST["st_age"]);
    $level  = trim($_POST["st_level"]);
    $phone = trim($_POST["st_phone"]);

    $check = $connect->prepare("SELECT st_id FROM student WHERE st_name = ? AND st_age = ? AND st_level = ? AND st_phone = ?");
    $check->bind_param("siss", $name, $age, $line, $phone);
    $check->execute();
    $check->store_result();

    if ($check->num_rows > 0) {
        $error = "هذا الطالب مسجل مسبقًا بهذه البيانات.";
    } else {
        $token = generateToken(8);
        $stmt = $connect->prepare("INSERT INTO student(st_token, st_name, st_age, st_level, st_phone) VALUES (?, ?, ?, ?, ?)");
        $stmt->bind_param("ssiss", $token, $name, $age, $level, $phone);

        if ($stmt->execute()) {
            // تخزين بيانات الطالب في الجلسة بعد التسجيل
            $_SESSION['st_name'] = $name;
            $_SESSION['st_level'] = $level;

            $success = "تم التسجيل بنجاح!";
        } else {
            $error = "حدث خطأ أثناء التسجيل.";
        }

        $stmt->close();
    }

    $check->close();
}
?>


<!DOCTYPE html>
<html lang="ar">
<head>
  <meta charset="UTF-8">
  <title>تسجيل طالب</title>
  <style>
    body {
      background-color: #f0f0f0;
      font-family: 'Cairo', sans-serif;
      direction: rtl;
    }
    .container {
      width: 400px;
      margin: 60px auto;
      background: white;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
    }
    h2 {
      text-align: center;
      color: #00bcd4;
      margin-bottom: 20px;
    }
    input {
      width: 100%;
      padding: 10px;
      margin-top: 12px;
      border: 1px solid #ccc;
      border-radius: 6px;
    }
    button {
      width: 100%;
      background-color: #00bcd4;
      color: white;
      padding: 12px;
      margin-top: 20px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      font-weight: bold;
    }
    button:hover {
      background-color: #0097a7;
    }
    .message {
      text-align: center;
      font-weight: bold;
      margin-top: 15px;
    }
    .success { color: green; }
    .error { color: red; }
  </style>

  
</head>
<body>

<div class="container">
  <h2>تسجيل طالب جديد</h2>

  <?php if ($success): ?>
    <div class="message success"><?= $success ?></div>
  <?php elseif ($error): ?>
    <div class="message error"><?= $error ?></div>
  <?php endif; ?>

  <form method="POST" onsubmit="return validateForm()">
    <input type="text" name="st_name" id="st_name" placeholder="اسم الطالب" required>
    <input type="number" name="st_age" id="st_age" placeholder="العمر" required min="5" max="100">
   
    <input type="text" name="st_phone" id="st_phone" placeholder="رقم الهاتف" required pattern="^07[0-9]{8,9}$">
    <label for="st_level">الصف الدراسي:</label>
<select name="st_level" id="st_level" required>
  <option value="">-- اختر الصف --</option>
  <option value="السادس اعدادي">السادس اعدادي</option>
  <option value="الثالث متوسط">الثالث متوسط</option>
</select>

    <button type="submit">تسجيل</button>
  </form>
</div>

<script>
function validateForm() {
  const name = document.getElementById("st_name").value.trim();
  const age = parseInt(document.getElementById("st_age").value);
  const phone = document.getElementById("st_phone").value.trim();

  if (name.length < 3) {
    alert("يرجى إدخال اسم صحيح.");
    return false;
  }

  if (isNaN(age) || age < 5 || age > 100) {
    alert("يرجى إدخال عمر صحيح بين 5 و100.");
    return false;
  }

  const phoneRegex = /^07[0-9]{8,9}$/;
  if (!phoneRegex.test(phone)) {
    alert("يرجى إدخال رقم هاتف صحيح يبدأ بـ 07.");
    return false;
  }

  return true;
}
document.addEventListener("DOMContentLoaded", function () {
  const form = document.querySelector("form");
  const submitBtn = form.querySelector("button[type='submit']");

  form.addEventListener("submit", function (e) {
    const confirmed = confirm("هل أنت متأكد أنك تريد التسجيل بهذه البيانات؟");

    if (!confirmed) {
      e.preventDefault(); // إلغاء الإرسال
      return;
    }

    // تعطيل الزر لمنع الضغط المتكرر
    submitBtn.disabled = true;
    submitBtn.innerText = "جاري الإرسال...";
  });
})
</script>


</body>
</html>
