<?php
require 'DENHUS.php';
$sql = "SELECT * FROM assignments ORDER BY uploaded_at DESC";
$result = mysqli_query($connect, $sql);
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>عرض الواجبات</title>
    <style>
        @import url('https://fonts.googleapis.com/css2?family=Cairo&display=swap');

        *, *::before, *::after {
            box-sizing: border-box;
        }

        body {
            font-family: 'Cairo', sans-serif;
            background: #e6f7fa;
            margin: 0;
            padding: 50px 20px;
            color: #222;
            min-height: 100vh;
        }

        .top-buttons {
            max-width: 960px;
            margin: 0 auto 40px auto;
            display: flex;
            justify-content: flex-start;
            gap: 15px;
            flex-wrap: wrap;
        }
        .btn {
            background: #00bcd4;
            color: white;
            padding: 12px 28px;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            box-shadow: 0 6px 20px rgba(0, 188, 212, 0.4);
            transition: background 0.3s ease, box-shadow 0.3s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn:hover {
            background: #0097a7;
            box-shadow: 0 10px 30px rgba(0, 151, 167, 0.6);
        }

        h2 {
            text-align: center;
            color: #00bcd4;
            font-size: 3rem;
            margin-bottom: 50px;
            font-weight: 800;
            letter-spacing: 1.5px;
        }

        .container {
            max-width: 960px;
            margin: 0 auto;
        }

        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fit,minmax(320px,1fr));
            gap: 30px;
        }

        .assignment-card {
            background: #fff;
            border-radius: 15px;
            padding: 25px 30px;
            box-shadow: 0 10px 30px rgba(0, 188, 212, 0.15);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            height: 100%;
        }
        .assignment-card:hover {
            box-shadow: 0 20px 45px rgba(0, 188, 212, 0.3);
            transform: translateY(-8px);
        }

        .assignment-title {
            font-size: 1.9rem;
            font-weight: 700;
            color: #007c91;
            margin-bottom: 15px;
            line-height: 1.2;
        }

        .assignment-description {
            flex-grow: 1;
            font-size: 1.1rem;
            color: #444;
            line-height: 1.6;
            margin-bottom: 25px;
            white-space: pre-line;
        }

        .assignment-date {
            font-size: 0.9rem;
            color: #777;
            margin-bottom: 25px;
            text-align: left;
            font-weight: 600;
            letter-spacing: 0.03em;
        }

        .download-link {
            align-self: flex-start;
            padding: 14px 30px;
            background: #00bcd4;
            color: #fff;
            border-radius: 50px;
            font-weight: 700;
            font-size: 1.1rem;
            text-decoration: none;
            box-shadow: 0 6px 20px rgba(0, 188, 212, 0.4);
            transition: background 0.3s ease, box-shadow 0.3s ease;
        }
        .download-link:hover {
            background: #0097a7;
            box-shadow: 0 10px 30px rgba(0, 151, 167, 0.6);
        }

        .no-assignments {
            text-align: center;
            color: #666;
            font-size: 1.3rem;
            font-weight: 600;
            margin-top: 60px;
        }

        @media (max-width: 400px) {
            body {
                padding: 30px 10px;
            }
            h2 {
                font-size: 2.2rem;
                margin-bottom: 30px;
            }
            .assignment-card {
                padding: 20px 20px;
            }
            .assignment-title {
                font-size: 1.5rem;
            }
            .download-link, .btn {
                font-size: 1rem;
                padding: 12px 25px;
            }
        }
    </style>
</head>
<body>
<div class="top-buttons">
    <a href="admin.php" class="btn" title="الرجوع إلى لوحة التحكم">⬅️ رجوع</a>
</div>

<div class="container">
    <h2>📚 الواجبات المرفوعة</h2>

    <?php if (mysqli_num_rows($result) > 0): ?>
        <div class="grid">
        <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <div class="assignment-card">
                <h3 class="assignment-title"><?= htmlspecialchars($row['title']) ?></h3>
                <p class="assignment-description"><?= nl2br(htmlspecialchars($row['description'])) ?></p>
                <p class="assignment-date">📅 تاريخ الرفع: <?= date("Y-m-d", strtotime($row['uploaded_at'])) ?></p>
                <a class="download-link" href="assignments/<?= rawurlencode($row['file_path']) ?>" target="_blank" download>
                    📥 تحميل الواجب
                </a>
            </div>
        <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p class="no-assignments">لا توجد واجبات مرفوعة حتى الآن.</p>
    <?php endif; ?>
</div>
</body>
</html>
