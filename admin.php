
<?php
// نفس كود PHP لجلب عدد الطلاب
session_start();
require "DENHUS.php";
mysqli_set_charset($connect, 'utf8');

if (!isset($_SESSION['admin_id'])) {
    header("Location: admin.php");
    exit();
}

$adQuery = "SELECT * FROM student";
$adRun = mysqli_query($connect, $adQuery);
$adNum = ($adRun) ? mysqli_num_rows($adRun) : 0;
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8" />
    <title>لوحة تحكم الأدمن</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <!-- Font Awesome -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" rel="stylesheet" />
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Tajawal:wght@400;700&display=swap" rel="stylesheet" />
    <style>
        body {
            font-family: 'Tajawal', sans-serif;
            background-color: #f5f7fa;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        header {
            background-color: #00bcd4;
            color: white;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        header h1 {
            margin: 0;
            font-size: 24px;
        }
        header a {
            color: white;
            font-size: 18px;
            text-decoration: none;
        }
        .main-content {
            flex: 1;
            display: flex;
            gap: 30px;
            padding: 30px;
        }
        .dashboard {
            flex: 3;
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }
        .card {
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s;
        }
        .card:hover {
            transform: translateY(-5px);
        }
        .card i {
            font-size: 40px;
            color: #00bcd4;
            margin-bottom: 15px;
            transition: transform 0.3s ease;
        }
        .card:hover i {
            transform: rotate(10deg);
        }
        .card p a {
            font-size: 20px;
            font-weight: bold;
            color: #192324ff;
            text-decoration: none;
            transition: 0.3s ease;
            display: inline-block;
        }
        .card p a:hover {
            color: #0097a7;
            transform: scale(1.05);
        }
        .card .count {
            font-size: 40px;
            font-weight: bold;
            color: #00bcd4;
            margin-bottom: 10px;
        }
        .sidebar {
            flex: 1;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            padding: 30px 20px;
            text-align: center;
            display: flex;
            flex-direction: column;
            justify-content: center;
        }
        .sidebar i {
            font-size: 60px;
            color: #007c91;
            margin-bottom: 20px;
        }
        .sidebar .count {
            font-size: 48px;
            font-weight: bold;
            color: #007c91;
            margin-bottom: 10px;
        }
        .sidebar p {
            font-size: 22px;
            font-weight: bold;
            color: #333;
            margin: 0;
        }
        /* Responsive */
        @media (max-width: 900px) {
            .main-content {
                flex-direction: column;
            }
            .sidebar {
                margin-top: 30px;
            }
        }
    </style>
</head>
<body>
<header>
    <h1>لوحة تحكم الأدمن</h1>
    <a href="admin_logout.php"><i class="fa-solid fa-arrow-right-from-bracket"></i> تسجيل الخروج</a>
</header>

<div class="main-content">
    <div class="dashboard">
        <div class="card">
            <i class="fa-solid fa-video"></i>
            <p><a href="upload.php">رفع الدروس</a></p>
        </div>

        <div class="card">
            <i class="fa-solid fa-upload"></i>
            <p><a href="upload_assignment.php">رفع الواجبات</a></p>
        </div>

        <div class="card">
            <i class="fa-solid fa-users"></i>
            <p><a href="online_st.php">المستخدمون المسجلون</a></p>
        </div>

        <div class="card">
            <i class="fa-solid fa-chalkboard-teacher"></i>
            <p><a href="view_all_videos.php">المحاضرات المرفوعه</a></p>
        </div>

        <div class="card">
            <i class="fa-solid fa-file-alt"></i>
            <p><a href="st_assignmint.php">واجبات الطلاب</a></p>
        </div>

        <div class="card">
            <i class="fa-solid fa-file-alt"></i>
            <p><a href="view_assignments.php">الواجبات المرفوعه</a></p>
        </div>
    </div>

    <div class="sidebar">
        <i class="fa-solid fa-user-graduate"></i>
        <p class="count"><?php echo $adNum; ?></p>
        <p>عدد الطلاب المسجلين</p>
    </div>
</div>

</body>
</html>
