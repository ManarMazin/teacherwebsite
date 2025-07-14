<?php
session_start();
require 'DENHUS.php';

// تحقق من دخول المعلم (يمكنك إضافة التحقق هنا)

// معالجة إدخال التقييم عند POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['homework_id'])) {
    $homework_id = intval($_POST['homework_id']);
    $grade = intval($_POST['grade']);
    $comment = trim($_POST['comment']);

    if ($grade < 0 || $grade > 100) {
        $error = "الدرجة يجب أن تكون بين 0 و 100.";
    } else {
        $stmt2 = $connect->prepare("SELECT id FROM homework_grades WHERE homework_id = ?");
        $stmt2->bind_param("i", $homework_id);
        $stmt2->execute();
        $res2 = $stmt2->get_result();

        if ($res2->num_rows > 0) {
            $stmt3 = $connect->prepare("UPDATE homework_grades SET grade = ?, teacher_comment = ?, graded_at = NOW() WHERE homework_id = ?");
            $stmt3->bind_param("isi", $grade, $comment, $homework_id);
            $stmt3->execute();
        } else {
            $stmt3 = $connect->prepare("INSERT INTO homework_grades (homework_id, grade, teacher_comment) VALUES (?, ?, ?)");
            $stmt3->bind_param("iis", $homework_id, $grade, $comment);
            $stmt3->execute();
        }
        $success = "تم حفظ التقييم بنجاح.";
    }
}

$query = "SELECT h.id AS homework_id, h.student_name, h.homework_title, h.filename, h.uploaded_at FROM homeworks h ORDER BY h.uploaded_at DESC";
$submissions = $connect->query($query);


// عرض الواجبات مع التقييم للطالب

$stmt = $connect->prepare("
  SELECT h.*, g.grade, g.teacher_comment
  FROM homeworks h
  LEFT JOIN homework_grades g ON h.id = g.homework_id
  WHERE h.level = ?
  ORDER BY h.uploaded_at DESC
");
$stmt->bind_param("s", $student_level);
$stmt->execute();
$result = $stmt->get_result();

?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>تقييم الواجبات - صفحة المعلم</title>
<style>
  body {
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    background: #f0f4f8;
    margin: 0; padding: 20px;
    direction: rtl;
    color: #333;
  }
  .container {
    max-width: 900px;
    margin: auto;
    background: #fff;
    padding: 30px 40px;
    border-radius: 15px;
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    min-height: 600px;
  }
  h1 {
    color: #007acc;
    margin-bottom: 30px;
    text-align: center;
  }
  table {
    width: 100%;
    border-collapse: collapse;
    margin-bottom: 30px;
  }
  th, td {
    border: 1px solid #ddd;
    padding: 12px;
    text-align: center;
    vertical-align: middle;
  }
  th {
    background-color: #00bcd4;
    color: white;
  }
  tr:nth-child(even) {
    background-color: #f9f9f9;
  }
  form {
    display: flex;
    flex-direction: column;
    gap: 8px;
  }
  input[type=number], textarea {
    padding: 8px;
    border-radius: 8px;
    border: 1px solid #ccc;
    font-size: 14px;
    resize: vertical;
  }
  button {
    background-color: #00bcd4;
    color: white;
    border: none;
    padding: 10px 15px;
    border-radius: 8px;
    font-weight: bold;
    cursor: pointer;
    transition: background-color 0.3s ease;
  }
  button:hover {
    background-color: #0097a7;
  }
  .message {
    text-align: center;
    margin-bottom: 20px;
    font-weight: bold;
  }
  .error {
    color: #d32f2f;
  }
  .success {
    color: #388e3c;
  }
  a.file-link {
    color: #007acc;
    text-decoration: none;
  }
  a.file-link:hover {
    text-decoration: underline;
  }
</style>
</head>
<body>
<div class="container">
  <h1>تقييم الواجبات المرسلة من الطلاب</h1>

  <?php if (!empty($error)): ?>
    <div class="message error"><?= htmlspecialchars($error) ?></div>
  <?php endif; ?>

  <?php if (!empty($success)): ?>
    <div class="message success"><?= htmlspecialchars($success) ?></div>
  <?php endif; ?>

  <?php if ($submissions && $submissions->num_rows > 0): ?>
    <table>
      <thead>
        <tr>
          <th>اسم الطالب</th>
          <th>عنوان الواجب</th>
          <th>ملف الواجب</th>
          <th>تاريخ الإرسال</th>
          <th>تقييم المعلم</th>
          <th>تعليق المعلم</th>
          <th>إدخال/تعديل التقييم</th>
        </tr>
      </thead>
      <tbody>
        <?php while ($row = $submissions->fetch_assoc()): ?>
          <tr>
            <td><?= htmlspecialchars($row['student_name']) ?></td>
            <td><?= htmlspecialchars($row['homework_title']) ?></td>
<td>
  <?php if (!empty($row['filename'])): ?>
    <a href="download.php?file=<?= rawurlencode($row['filename']) ?>" target="_blank" class="file-link">📄 عرض الملف</a> |
    <a href="download.php?file=<?= rawurlencode($row['filename']) ?>" download class="file-link">📥 تحميل</a>
  <?php else: ?>
    لا يوجد ملف
  <?php endif; ?>
</td>




            <td><?= htmlspecialchars($row['uploaded_at']) ?></td>

            <?php
              $homework_id = intval($row['homework_id']);
              $grade = '';
              $comment = '';
              $grade_stmt = $connect->prepare("SELECT grade, teacher_comment FROM homework_grades WHERE homework_id = ? LIMIT 1");
              $grade_stmt->bind_param("i", $homework_id);
              $grade_stmt->execute();
              $grade_res = $grade_stmt->get_result();
              if ($grade_res && $grade_res->num_rows > 0) {
                $grade_row = $grade_res->fetch_assoc();
                $grade = $grade_row['grade'];
                $comment = $grade_row['teacher_comment'];
              }
            ?>

            <td><?= $grade !== '' ? intval($grade) . " / 100" : 'لم يتم التقييم' ?></td>
            <td><?= $comment !== '' ? nl2br(htmlspecialchars($comment)) : 'لا يوجد تعليق' ?></td>

            <td>
              <form method="POST" style="min-width: 200px;">
                <input type="hidden" name="homework_id" value="<?= $homework_id ?>">
                <input type="number" name="grade" min="0" max="100" placeholder="الدرجة (0-100)" value="<?= htmlspecialchars($grade) ?>" required>
                <textarea name="comment" rows="3" placeholder="تعليق المعلم"><?= htmlspecialchars($comment) ?></textarea>
                <button type="submit">حفظ التقييم</button>
              </form>
            </td>
          </tr>
        <?php endwhile; ?>
      </tbody>
    </table>
  <?php else: ?>
    <p style="text-align:center; font-size:1.2em; color:#555;">لا توجد واجبات تم إرسالها حتى الآن.</p>
  <?php endif; ?>

</div>
</body>
</html>
