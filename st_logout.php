<?php
session_start();
$_SESSION = array();
session_destroy();
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <title>تسجيل الخروج</title>
  <meta http-equiv="refresh" content="3;url=st_login.php">
  <style>
    body {
      background-color: #f0f0f0;
      font-family: 'Cairo', sans-serif;
      display: flex;
      align-items: center;
      justify-content: center;
      height: 100vh;
    }
    .message-box {
      background-color: #ffffff;
      padding: 30px;
      border-radius: 12px;
      box-shadow: 0 0 12px rgba(0,0,0,0.1);
      text-align: center;
    }
    .message-box h2 {
      color: #00bcd4;
    }
    .message-box p {
      color: #333;
      margin-top: 10px;
    }
  </style>
</head>
<body>

  <div class="message-box">
    <h2>تم تسجيل الخروج بنجاح</h2>
    <p>سيتم تحويلك إلى صفحة تسجيل الدخول خلال لحظات...</p>
  </div>

</body>
</html>

