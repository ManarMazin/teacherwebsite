<?php
session_start();
require 'DENHUS.php';

if (!isset($_SESSION['st_name']) || !isset($_SESSION['st_level'])) {
    echo "<p style='color:red; text-align:center; margin-top:50px;'>يرجى تسجيل الدخول أولاً.</p>";
    exit;
}

$student_name = $_SESSION['st_name'];
$student_level = $_SESSION['st_level'];

// تعديل الاستعلام لجلب الواجبات مع تقييم المعلم (إن وجد)
$stmt = $connect->prepare("
  SELECT a.*, g.grade, g.teacher_comment 
  FROM assignments a 
  LEFT JOIN homework_grades g ON a.id = g.homework_id 
  WHERE a.level = ? 
  ORDER BY a.uploaded_at DESC
");
$stmt->bind_param("s", $student_level);
$stmt->execute();
$assignments = $stmt->get_result();

function safeFilePath($path) {
    if (strpos($path, '../') !== false) return '';
    if (strpos($path, 'assignments/') === 0) {
        return 'assignments/' . rawurlencode(substr($path, strlen('assignments/')));
    }
    return htmlspecialchars($path);
}
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>الواجبات - <?= htmlspecialchars($student_level) ?></title>
<style>
body {
    font-family: "Segoe UI", Tahoma, Geneva, Verdana, sans-serif;
    background: #f0f4f8;
    margin: 0; padding: 20px;
    direction: rtl;
    color: #333;
}
.header-box {
    background-color: #f0f8ff;
    padding: 20px;
    margin-bottom: 20px;
    border-radius: 15px;
    box-shadow: 0 0 10px #ccc;
    display: flex;
    flex-direction: column;
    gap: 10px;
}
.header-box .info h1,
.header-box .info h3 {
    margin: 0;
    color: #333;
}
.header-box .info span {
    color: #007acc;
    font-weight: bold;
}
.header-box .actions {
    display: flex;
    justify-content: space-between;
    margin-top: 10px;
    flex-wrap: wrap;
    gap: 10px;
}
.btn-back,
.btn-submitted {
    text-decoration: none;
    background-color: #00bcd4;
    color: white;
    padding: 10px 15px;
    border-radius: 8px;
    font-weight: bold;
    transition: background 0.3s ease;
    min-width: 180px;
    text-align: center;
}
.btn-back:hover,
.btn-submitted:hover {
    background-color: #0097a7;
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
.assignment {
    background: #fafafa;
    border: 1px solid #ddd;
    border-radius: 12px;
    padding: 20px 25px;
    margin-bottom: 25px;
    box-shadow: 0 0 10px rgba(0,0,0,0.03);
    display: flex;
    justify-content: space-between;
    align-items: center;
    gap: 10px;
    flex-wrap: wrap;
    transition: box-shadow 0.3s ease;
}
.assignment:hover {
    box-shadow: 0 0 20px rgba(0,122,204,0.2);
}
.assignment .details {
    max-width: 60%;
    min-width: 300px;
}
.assignment h3 {
    margin-top: 0;
    color: #005f99;
}
.assignment p {
    line-height: 1.6;
    margin: 10px 0 0 0;
}
button.show-file {
    background-color: #007acc;
    color: #fff;
    border: none;
    padding: 10px 18px;
    border-radius: 10px;
    font-weight: 600;
    cursor: pointer;
    transition: background-color 0.3s ease;
    white-space: nowrap;
}
button.show-file:hover {
    background-color: #005f99;
}
a.submit-btn {
    background-color: #00bcd4;
    color: white;
    padding: 10px 20px;
    border-radius: 10px;
    font-weight: 600;
    text-decoration: none;
    transition: background-color 0.3s ease;
    white-space: nowrap;
}
a.submit-btn:hover {
    background-color: #0097a7;
}
.no-assignments {
    text-align: center;
    font-size: 1.2em;
    color: #555;
    margin-top: 60px;
}
#fileViewer {
    display: none;
    position: fixed;
    top: 60px;
    right: 20px;
    width: 40%;
    height: 80%;
    background: #fff;
    border-radius: 15px;
    box-shadow: 0 0 25px rgba(0,0,0,0.25);
    z-index: 1000;
    overflow: hidden;
    flex-direction: column;
}
#fileViewer header {
    background: #007acc;
    color: white;
    padding: 12px 20px;
    font-weight: bold;
    display: flex;
    justify-content: space-between;
    align-items: center;
    border-top-left-radius: 15px;
    border-top-right-radius: 15px;
}
#fileViewer header button.close-btn {
    background: transparent;
    border: none;
    font-size: 22px;
    color: white;
    cursor: pointer;
}
#fileFrame {
    width: 100%;
    height: calc(100% - 48px);
    border: none;
    display: block;
}
#fileImage {
    display: none;
    width: 100%;
    height: calc(100% - 48px);
    object-fit: contain;
    background: #eee;
}
/* Responsive for tablets */
@media screen and (max-width: 900px) {
    #fileViewer {
        width: 90%;
        height: 60%;
        top: 70px;
        right: 5%;
    }
    .assignment .details {
        max-width: 100%;
        min-width: auto;
    }
}
/* Responsive for small devices */
@media screen and (max-width: 600px) {
    .assignment {
        flex-direction: column;
        align-items: flex-start;
    }
    .assignment .details {
        width: 100%;
    }
    .assignment button,
    .assignment a.submit-btn {
        width: 100%;
        text-align: center;
    }
    .header-box .actions {
        flex-direction: column;
        gap: 10px;
    }
    .btn-back,
    .btn-submitted {
        width: 100%;
    }
    .container {
        padding: 20px;
    }
    #fileViewer {
        width: 95%;
        height: 60%;
        right: 2.5%;
    }
}
</style>
</head>
<body>

<div class="container">
    <div class="header-box">
        <div class="info">
            <h1>📚 الواجبات الدراسية للصف: <span><?= htmlspecialchars($student_level) ?></span></h1>
            <h3 class="welcome">👋 مرحبًا، <span><?= htmlspecialchars($student_name) ?></span></h3>
        </div>
        <div class="actions">
            <a href="hw_send.php" class="btn-submitted">📄 عرض الواجبات المرسلة</a>
            <a href="st_dashboard.php" class="btn-back">🔙 الرجوع للوحة التحكم</a>
        </div>
    </div>

    <?php if ($assignments->num_rows > 0): ?>
        <?php while ($row = $assignments->fetch_assoc()): ?>
            <div class="assignment">
                <div class="details">
                    <h3><?= htmlspecialchars($row['title']) ?></h3>
                    <p><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                  <?php if (!empty($row['grade'])): ?>
  <div style="margin-top:10px; font-weight:bold; color:green;">
    ✅ تم التقييم: <strong><?= intval($row['grade']) ?>/100</strong><br>
    ✍️ تعليق المعلم: <?= nl2br(htmlspecialchars($row['teacher_comment'])) ?>
  </div>

<?php endif; ?>

                </div>
                <div style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
                    <?php if (!empty($row['file_path'])): ?>
                        <button class="show-file" onclick="showFile('<?= safeFilePath($row['file_path']) ?>', '<?= htmlspecialchars(addslashes($row['title'])) ?>', 'file')">📄 عرض الملف</button>
                    <?php else: ?>
                        <span style="color:#999;">لا يوجد ملف</span>
                    <?php endif; ?>

                    <?php if (!empty($row['image_path'])): ?>
                        <button class="show-file" onclick="showFile('<?= safeFilePath($row['image_path']) ?>', '<?= htmlspecialchars(addslashes($row['title'])) ?>', 'image')">🖼️ عرض الصورة</button>
                    <?php else: ?>
                        <span style="color:#999;">لا توجد صورة</span>
                    <?php endif; ?>

                    <a href="upload_homework.php?assignment_id=<?= $row['id'] ?>" class="submit-btn">📤 إرسال الواجب</a>
                </div>
            </div>
        <?php endwhile; ?>
    <?php else: ?>
        <p class="no-assignments">لا توجد واجبات لهذا الصف حالياً.</p>
    <?php endif; ?>
</div>

<!-- نافذة عرض الملف / الصورة -->
<div id="fileViewer">
    <header>
        <span id="fileTitle">اسم الملف</span>
        <button class="close-btn" onclick="closeFileViewer()">×</button>
    </header>
    <iframe id="fileFrame" src=""></iframe>
    <img id="fileImage" src="" alt="عرض الصورة" />
</div>

<script>
function showFile(url, title, type) {
    const viewer = document.getElementById('fileViewer');
    const frame = document.getElementById('fileFrame');
    const img = document.getElementById('fileImage');
    const titleElem = document.getElementById('fileTitle');

    titleElem.textContent = title;

    if(type === 'image') {
        frame.style.display = 'none';
        img.style.display = 'block';
        img.src = url;
    } else {
        img.style.display = 'none';
        frame.style.display = 'block';
        frame.src = url;
    }

    viewer.style.display = 'flex';
}
function closeFileViewer() {
    const viewer = document.getElementById('fileViewer');
    document.getElementById('fileFrame').src = '';
    document.getElementById('fileImage').src = '';
    viewer.style.display = 'none';
}
</script>

</body>
</html>
