<?php
session_start();

require 'DENHUS.php';

if (!isset($_SESSION['st_name'])) {
    echo "<p style='color:red; text-align:center; margin-top:50px;'>يرجى تسجيل الدخول أولاً.</p>";
    exit;
}

// عرض الإشعار عند وجود تقييم جديد
if (isset($_SESSION['grade_success'])) {
    echo '
    <div id="alertBox" style="background-color:#d4edda; color:#155724; padding:15px; text-align:center; border-radius:10px; margin:20px auto; width:90%; max-width:800px; font-weight:bold; position:relative; box-shadow:0 4px 10px rgba(0,0,0,0.05); transition: opacity 0.5s;">
        <span style="position:absolute; top:5px; right:15px; cursor:pointer; font-size:18px;" onclick="document.getElementById(\'alertBox\').style.display=\'none\';">&times;</span>
        ✅ ' . $_SESSION['grade_success'] . '
    </div>
    <script>
        setTimeout(function() {
            var box = document.getElementById("alertBox");
            if (box) {
                box.style.opacity = "0";
                setTimeout(function() { box.style.display = "none"; }, 500);
            }
        }, 5000);
    </script>';
    unset($_SESSION['grade_success']);
}

$student_name = $_SESSION['st_name'];

$stmt = $connect->prepare("
  SELECT h.*, g.grade, g.teacher_comment
  FROM homeworks h
  LEFT JOIN homework_grades g ON h.id = g.homework_id
  WHERE h.student_name = ?
  ORDER BY h.uploaded_at DESC
");
$stmt->bind_param("s", $student_name);
$stmt->execute();
$result = $stmt->get_result();
?>


<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>الواجبات المرسلة</title>
<style>
body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f0f4f8;
    margin: 0; padding: 20px;
    direction: rtl;
}
.container {
    max-width: 900px;
    margin: auto;
    background: #fff;
    padding: 30px 40px;
    border-radius: 15px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}
/* ترتيب العنوان وزر الرجوع */
.header-flex {
  display: flex;
  justify-content: space-between;
  align-items: center;
  margin-bottom: 30px;
}
.header-flex h1 {
  margin: 0;
  font-size: 1.8em;
  color: #007acc;
}
.btn-back {
  text-decoration: none;
  background-color: #00bcd4;
  color: white;
  padding: 10px 20px;
  border-radius: 8px;
  font-weight: bold;
  transition: background 0.3s ease;
  min-width: 180px;
  text-align: center;
  user-select: none;
  cursor: pointer;
  box-shadow: 0 4px 10px rgba(0,188,212,0.4);
}
.btn-back:hover {
  background-color: #0097a7;
  box-shadow: 0 6px 15px rgba(0,151,167,0.6);
}

.assignment {
    background: #fafafa;
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 20px 25px;
    margin-bottom: 25px;
}
.assignment h3 {
    margin-top: 0;
    color: #005f99;
}
.assignment p {
    line-height: 1.6;
    margin: 10px 0 0 0;
}
.grade-box {
    margin-top: 15px;
    padding: 10px;
    border-radius: 8px;
    background: #e8f5e9;
    color: #2e7d32;
    font-weight: bold;
}
.no-grade {
    margin-top: 15px;
    padding: 10px;
    border-radius: 8px;
    background: #fff3e0;
    color: #ef6c00;
    font-weight: bold;
}
a.download-link {
    display: inline-block;
    margin-top: 10px;
    background-color: #00bcd4;
    color: white;
    padding: 8px 14px;
    border-radius: 8px;
    text-decoration: none;
}
a.download-link:hover {
    background-color: #0097a7;
}
</style>
</head>
<body>

<div class="container">
  <div class="header-flex">
    <h1>📄 الواجبات التي أرسلتها</h1>
    <a href="show_hw.php" class="btn-back">🔙 الرجوع للصفحة السابقة</a>
  </div>

  <?php if ($result->num_rows > 0): ?>
      <?php while ($row = $result->fetch_assoc()): ?>
          <div class="assignment">
              <h3><?= htmlspecialchars($row['homework_title']) ?></h3>
              <p>📅 تاريخ الإرسال: <?= htmlspecialchars($row['uploaded_at']) ?></p>

              <?php if (!empty($row['filename'])): ?>
                  <a class="download-link" href="download.php?file=<?= rawurlencode($row['filename']) ?>" target="_blank">📥 عرض أو تحميل الملف</a>
              <?php else: ?>
                  <p style="color:#999;">لا يوجد ملف مرفوع</p>
              <?php endif; ?>

              <?php if (isset($row['grade'])): ?>
                  <div class="grade-box">
                      ✅ تم التقييم: <?= intval($row['grade']) ?>/100<br>
                      ✍️ التعليق: <?= nl2br(htmlspecialchars($row['teacher_comment'])) ?>
                  </div>
              <?php else: ?>
                  <div class="no-grade">⏳ لم يتم تقييم هذا الواجب بعد.</div>
              <?php endif; ?>
          </div>
      <?php endwhile; ?>
  <?php else: ?>
      <p style="text-align:center; font-size:1.2em; color:#555;">لم تقم بإرسال أي واجب حتى الآن.</p>
  <?php endif; ?>
</div>

</body>
</html>


