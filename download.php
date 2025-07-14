<?php
if (!isset($_GET['file'])) {
    die("لم يتم تحديد الملف.");
}

$filename = basename($_GET['file']); // حماية من المسارات الخبيثة
$filepath = __DIR__ . "/homeworks/" . $filename;

if (!file_exists($filepath)) {
    die("الملف غير موجود.");
}

// تحديد نوع الملف
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $filepath);
finfo_close($finfo);

header("Content-Type: $mime");
header("Content-Disposition: inline; filename=\"$filename\"");
header("Content-Length: " . filesize($filepath));
readfile($filepath);
exit;
?>

