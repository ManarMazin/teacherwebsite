<?php
session_start();
if (!isset($_SESSION['st_name']) || !isset($_SESSION['st_level'])) {
    header("Location: st_login.php");
    exit;
}
$name = $_SESSION['st_name'];
$level = $_SESSION['st_level'];

require "DENHUS.php"; // اتصال بقاعدة البيانات

// استعلام لأحدث المحاضرات
$lessons = mysqli_query($connect, "SELECT * FROM videos WHERE level='$level' ORDER BY uploaded_at DESC LIMIT 1");

// استعلام لأحدث الواجبات
$assignments = mysqli_query($connect, "SELECT * FROM assignments WHERE level='$level' ORDER BY uploaded_at DESC LIMIT 1");
?>
<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <title>لوحة تحكم الطالب</title>
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <style>
    body {
      margin: 0;
      font-family: 'Segoe UI', sans-serif;
      background-color: #f2f2f2;
      color: #333;
    }

    header {
      background-color: #00bcd4;
      color: white;
      padding: 20px 30px;
      box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
      display: flex;
      align-items: center;
      justify-content: center;
      position: relative;
    }

    header h1 {
      margin: 0;
      font-size: 26px;
      text-align: right;
      flex-grow: 1;
    }

    /* زر تسجيل الخروج */
    .btn-back {
      position: absolute;
      left: 30px; /* جهة اليسار لأن الصفحة rtl */
      background: transparent;
      border: 2.5px solid white;
      border-radius: 6px;
      padding: 6px 16px;
      color: white;
      font-weight: 700;
      font-size: 16px;
      cursor: pointer;
      display: flex;
      align-items: center;
      gap: 6px;
      text-decoration: none;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .btn-back:hover {
      background-color: white;
      color: #0097a7;
      text-decoration: none;
    }

    .btn-back svg {
      width: 20px;
      height: 20px;
      fill: currentColor;
    }

    main {
      max-width: 900px;
      margin: 30px auto;
      background-color: #fff;
      border-radius: 12px;
      padding: 30px 20px;
      box-shadow: 0 5px 15px rgba(0,0,0,0.05);
    }

    .welcome {
      font-size: 20px;
      margin-bottom: 30px;
      color: #444;
      text-align: center;
    }

    .btn-grid {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      margin-bottom: 30px;
    }

  .btn {
  background-color: #00bcd4;
  color: white;
  text-decoration: none;
  padding: 20px 80px; /* أطول قليلاً */
  border-radius: 10px;
  font-size: 18px;
  transition: 0.3s;
}


    .btn:hover {
      background-color: #0097a7;
    }

    table {
      width: 100%;
      border-collapse: collapse;
      margin-top: 20px;
    }

    th, td {
      border: 1px solid #ddd;
      padding: 12px;
      text-align: center;
    }

    th {
      background-color: #00bcd4;
      color: white;
    }

    tr:nth-child(even) {
      background-color: #f9f9f9;
    }

    .section-title {
      margin-top: 40px;
      font-size: 20px;
      color: #333;
    }

    footer {
      margin-top: 50px;
      text-align: center;
      color: #999;
      font-size: 13px;
    }

    @media (max-width: 600px) {
      .btn-grid {
        flex-direction: column;
      }

      table, thead, tbody, th, td, tr {
        font-size: 14px;
      }
    }

    .show-more-link {
      display: inline-block;
      padding: 6px 14px;
      font-size: 14px;
      color: #00bcd4;
      text-decoration: none;
      border: 1.5px solid #00bcd4;
      border-radius: 6px;
      transition: background-color 0.3s ease, color 0.3s ease;
    }

    .show-more-link:hover {
      background-color: #00bcd4;
      color: white;
      text-decoration: none;
    }
  </style>
</head>
<body>

<header>
  <h1>لوحة تحكم الطالب</h1>
  <a class="btn-back" href="st_logout.php" aria-label="تسجيل الخروج">
    🚪 تسجيل الخروج
  </a>
</header>
<main>
  <div class="welcome">
    مرحبًا، <strong><?php echo htmlspecialchars($name); ?></strong> 👋 | الصف: <strong><?php echo htmlspecialchars($level); ?></strong>
  </div>

  <div class="btn-grid">
    <a class="btn" href="lessons.php">📘 المحاضرات</a>
    <a class="btn" href="show_hw.php">📝 الواجبات</a>
   
  </div>

  <h2 class="section-title">📘 أحدث المحاضرات</h2>
<table>
  <tr>
    <th>العنوان</th>
    <th>الوصف</th>
    <th>تاريخ الرفع</th>
    <th>مشاهدة</th>
  </tr>
  <?php while ($row = mysqli_fetch_assoc($lessons)): ?>
    <tr>
      <td><?php echo htmlspecialchars($row['title']); ?></td>
      <td><?php echo htmlspecialchars($row['description']); ?></td>
      <td><?php echo htmlspecialchars($row['uploaded_at']); ?></td>
      <td>
        <a class="show-more-link" href="lessons.php">عرض المزيد</a>
      </td>
    </tr>
  <?php endwhile; ?>
</table>


  <h2 class="section-title">📝 أحدث الواجبات</h2>
<table>
  <tr>
    <th>العنوان</th>
    <th>الوصف</th>
    <th>تاريخ النشر</th>
    <th>مشاهدة</th>
  </tr>
  <?php while ($hw = mysqli_fetch_assoc($assignments)): ?>
    <tr>
      <td><?php echo htmlspecialchars($hw['title']); ?></td>
      <td><?php echo htmlspecialchars($hw['description']); ?></td>
      <td><?php echo htmlspecialchars($hw['uploaded_at']); ?></td>
      <td>
  <a class="show-more-link" href="show_hw.php">عرض المزيد</a>
</td>

    </tr>
  <?php endwhile; ?>
</table>
</main>

<footer>
  © منصة التعليم - جميع الحقوق محفوظة 2025
</footer>

</body>
</html>
