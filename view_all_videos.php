<?php
session_start();
require 'DENHUS.php'; // الاتصال بقاعدة البيانات

// حذف فيديو عند الطلب
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $delete_sql = "DELETE FROM videos WHERE id = $delete_id";
    mysqli_query($connect, $delete_sql);
}

// جلب كل الفيديوهات
$sql = "SELECT id, title, course_name, category, level, uploaded_at FROM videos ORDER BY uploaded_at DESC";
$result = mysqli_query($connect, $sql);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8">
    <title>عرض الفيديوهات</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            background-color: #f0f8ff;
            direction: rtl;
            margin: 0;
            padding: 0;
        }
        .header {
            background-color: #3d8fa5ff;
            padding: 15px;
            color: white;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .header h2 {
            margin: 0;
        }
        .header a.back-btn {
            background-color: white;
            color: #3d8fa5ff;
            padding: 8px 15px;
            border-radius: 5px;
            text-decoration: none;
            font-weight: bold;
        }
        .header a.back-btn:hover {
            background-color: #e6f7ff;
        }
        .container {
            max-width: 1000px;
            margin: 30px auto;
            background-color: #fff;
            padding: 25px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0,0,0,0.1);
        }
        .video-card {
            border: 1px solid #ddd;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
        }
        .video-card h3 {
            margin-top: 0;
            color: #03a9f4;
        }
        .video-card p {
            margin: 5px 0;
        }
        .delete-btn {
            background-color: #dc3545;
            color: white;
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
            margin-left: 10px;
        }
        .edit-btn {
            background-color: #00bcd4;
            color: white;
            padding: 6px 12px;
            border-radius: 5px;
            text-decoration: none;
        }
        .delete-btn:hover,
        .edit-btn:hover {
            opacity: 0.9;
        }
    </style>
</head>
<body>

<div class="header">
    <h2>جميع الفيديوهات</h2>
    <a href="admin.php" class="back-btn">رجوع</a>
</div>

<div class="container">
    <?php if (mysqli_num_rows($result) === 0): ?>
        <p>لا توجد فيديوهات لعرضها.</p>
    <?php else: ?>
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="video-card">
                <h3><?= htmlspecialchars($row['title']) ?></h3>
                <p>📘 الدورة: <?= htmlspecialchars($row['course_name']) ?></p>
                <p>🗂️ التخصص: <?= htmlspecialchars($row['category']) ?> | المستوى: <?= htmlspecialchars($row['level']) ?></p>
                <p>📅 تاريخ الرفع: <?= htmlspecialchars($row['uploaded_at']) ?></p>

                <a href="edit_video.php?id=<?= $row['id'] ?>" class="edit-btn">✏️ تعديل</a>
                <a href="?delete_id=<?= $row['id'] ?>" class="delete-btn" onclick="return confirm('هل أنت متأكد من حذف هذا الفيديو؟')">🗑️ حذف</a>
            </div>
        <?php endwhile; ?>
    <?php endif; ?>
</div>

</body>
</html>
