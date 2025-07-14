<?php
require 'DENHUS.php';
mysqli_set_charset($connect, 'utf8');

if (isset($_GET['delete_token'])) {
    $token = mysqli_real_escape_string($connect, $_GET['delete_token']);
    $delete = mysqli_query($connect, "DELETE FROM student WHERE st_token = '$token'");
    if ($delete) {
        header("Location: online_st.php?msg=تم حذف الطالب بنجاح&type=success");
        exit;
    } else {
        header("Location: online_st.php?msg=حدث خطأ أثناء الحذف&type=error");
        exit;
    }
}

// ثم جلب الطلاب كالمعتاد
$query = "SELECT st_token, st_age, st_level, st_name, st_phone FROM student";
$result = mysqli_query($connect, $query);
?>

<!DOCTYPE html>
<html lang="ar">
<head>
    <meta charset="UTF-8" />
    <title>قائمة المستخدمين</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
    <style>
        body {
            font-family: 'Cairo', sans-serif;
            background-color: rgb(250, 250, 250);
            direction: rtl;
            padding: 40px;
        }

        .header-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 30px;
        }

        h2 {
            margin: 0;
            color: #00bcd4;
            font-weight: 700;
            font-size: 28px;
        }

        .btn-back {
            padding: 10px 25px;
            background-color: #00bcd4;
            color: white;
            font-weight: bold;
            text-decoration: none;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 188, 212, 0.3);
            transition: background-color 0.3s ease, box-shadow 0.3s ease;
        }

        .btn-back:hover {
            background-color: #009eb3;
            box-shadow: 0 6px 12px rgba(0, 158, 179, 0.6);
        }

        .message {
            padding: 15px;
            margin-bottom: 20px;
            text-align: center;
            border-radius: 6px;
            font-weight: bold;
        }
        .message.success { background-color: #d4edda; color: #155724; }
        .message.error { background-color: #f8d7da; color: #721c24; }
        .message.info { background-color: #d1ecf1; color: #0c5460; }

        table {
            width: 100%;
            border-collapse: collapse;
            background-color: #fff;
            border-radius: 12px;
            box-shadow: 0 2px 10px rgba(0, 188, 212, 0.1);
            overflow: hidden;
        }

        th, td {
            padding: 14px;
            text-align: center;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #00bcd4;
            color: #fff;
        }

        tr:hover {
            background-color: #f9f9f9;
        }

        a {
            text-decoration: none;
            font-weight: bold;
        }

        .add-btn {
            display: inline-block;
            margin-bottom: 20px;
            background-color: #00bcd4;
            color: white;
            padding: 10px 20px;
            border-radius: 8px;
            text-decoration: none;
            font-size: 16px;
        }

        .add-btn:hover {
            background-color: #009eb3;
        }
    </style>
</head>
<body>

<div class="header-container">
    <h2><i class="fa-solid fa-users"></i> بيانات المستخدمين</h2>
    <a class="btn-back" href="admin.php">← رجوع</a>
</div>

<?php if (isset($_GET['msg'])): ?>
    <div class="message <?= $_GET['type'] ?? 'info' ?>">
        <?= htmlspecialchars($_GET['msg']) ?>
    </div>
<?php endif; ?>


<table>
    <thead>
        <tr>
            <th>الاسم</th>
            <th>رمز الدخول</th>
            <th>الصف</th>
            <th>رقم الهاتف</th>
            <th>الإجراءات</th>
        </tr>
    </thead>
    <tbody>
        <?php if (mysqli_num_rows($result) > 0): ?>
            <?php while ($row = mysqli_fetch_assoc($result)): ?>
                <tr>
                    <td><?= htmlspecialchars($row['st_name']) ?></td>
                    <td><?= htmlspecialchars($row['st_token']) ?></td>
                    <td><?= htmlspecialchars($row['st_level']) ?></td>
                    <td><?= htmlspecialchars($row['st_phone']) ?></td>
                    <td>
                        <a href="st_edit.php?token=<?= $row['st_token'] ?>" style="color:#00bcd4;">
                            <i class="fas fa-edit"></i> تعديل
                        </a> |
                        <a href="online_st.php?delete_token=<?= $row['st_token'] ?>" 
   onclick="return confirm('هل أنت متأكد من حذف هذا الطالب؟');" 
   style="color:red;">
   <i class="fas fa-trash-alt"></i> حذف
</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr><td colspan="5">لا يوجد مستخدمون في قاعدة البيانات.</td></tr>
        <?php endif; ?>
    </tbody>
</table>

</body>
</html>
