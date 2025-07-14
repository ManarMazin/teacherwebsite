<?php
session_start();

if (!isset($_SESSION['st_level'])) {
    header("Location: st_login.php");
    exit;
}

$student_level = $_SESSION['st_level'];

require "DENHUS.php";
mysqli_set_charset($connect, 'utf8');

$student_level_safe = mysqli_real_escape_string($connect, $student_level);

$school_videos = mysqli_query($connect, "SELECT * FROM videos 
    WHERE category NOT LIKE '%صيف%' 
    AND level LIKE '%$student_level_safe%'
    ORDER BY category, uploaded_at DESC");
?>

<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1" />
<title>الدروس التعليمية</title>
<style>
  body {
    font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background: #f7fbfc;
    margin: 0;
    direction: rtl;
    color: #333;
  }
  /* رأس الصفحة */
  .header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 15px 25px;
    background: linear-gradient(90deg, #00bcd4, #0097a7);
    color: white;
    font-weight: 700;
    font-size: 22px;
  }
  .header .title {
    user-select: none;
  }
  .btn-back {
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
    transition: background-color 0.3s ease, color 0.3s ease;
  }
  .btn-back:hover {
    background-color: white;
    color: #0097a7;
  }
  .btn-back svg {
    width: 20px;
    height: 20px;
    fill: currentColor;
  }

  /* حاوية المحتوى */
  .container {
    max-width: 960px;
    margin: 25px auto 60px auto;
    padding: 0 20px;
  }

  .container h2 {
    color: #007acc;
    margin-bottom: 20px;
    font-weight: 700;
    border-bottom: 3px solid #00bcd4;
    padding-bottom: 10px;
  }

  /* بطاقات الفيديو */
  .video-card {
    background-color: white;
    padding: 18px 22px;
    margin-bottom: 15px;
    border-radius: 15px;
    box-shadow: 0 8px 20px rgba(0,0,0,0.07);
    display: flex;
    gap: 20px;
    align-items: center;
    transition: box-shadow 0.3s ease;
    cursor: pointer;
  }
  .video-card:hover {
    box-shadow: 0 12px 28px rgba(0,0,0,0.12);
  }

  .video-thumb {
    flex-shrink: 0;
    width: 170px;
    height: 95px;
    background-color: #000;
    border-radius: 12px;
    object-fit: cover;
    box-shadow: 0 4px 12px rgba(0,0,0,0.15);
  }

  .video-info {
    flex-grow: 1;
  }

  .video-title {
    color: #007acc;
    font-weight: 800;
    font-size: 20px;
    margin-bottom: 6px;
    user-select: none;
  }

  .video-meta {
    color: #555;
    font-size: 14px;
    margin-bottom: 10px;
  }

  .video-description {
    font-size: 15px;
    color: #444;
    line-height: 1.5;
    user-select: none;
  }

  /* مودال الفيديو */
  .modal {
    display: none; 
    position: fixed; 
    z-index: 1100; 
    left: 0;
    top: 0;
    width: 100%; 
    height: 100%; 
    background-color: rgba(0,0,0,0.85);
    align-items: center;
    justify-content: center;
  }
  .modal-content {
    background-color: transparent;
    border-radius: 14px;
    max-width: 80%;
    max-height: 80%;
    position: relative;
    box-shadow: 0 0 50px rgba(0,0,0,0.7);
  }
  .modal video {
    width: 100%;
    height: auto;
    border-radius: 14px;
    box-shadow: 0 8px 30px rgba(0,0,0,0.5);
  }
  .close-btn {
    position: absolute;
    top: -40px;
    right: 0;
    font-size: 38px;
    font-weight: bold;
    color: white;
    cursor: pointer;
    user-select: none;
    transition: color 0.3s;
    padding: 4px 12px;
  }
  .close-btn:hover {
    color: #00bcd4;
  }

  /* رابط مشاهدة خارجي */
  .watch-link {
    display: inline-block;
    margin-top: 8px;
    color: #00bcd4;
    font-weight: 700;
    text-decoration: none;
    font-size: 15px;
  }
  .watch-link:hover {
    text-decoration: underline;
  }

</style>
</head>
<body>

<header class="header" role="banner">
  <div class="title" aria-label="عنوان الصفحة">الدروس التعليمية - الصف السادس الإعدادي</div>
  <button class="btn-back" onclick="window.history.back()" aria-label="الرجوع للصفحة السابقة">
    <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2" aria-hidden="true" focusable="false">
      <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
    </svg>
    رجوع
  </button>
</header>


<div class="container" id="school">
  <h2>📘 المناهج الدراسية</h2>

  <?php while($video = mysqli_fetch_assoc($school_videos)): ?>
    <?php
      $video_src = '';
      if (!empty($video['filename']) && file_exists('uploads/' . $video['filename'])) {
        $video_src = 'uploads/' . $video['filename'];
      } elseif (!empty($video['video_url'])) {
        $video_src = $video['video_url'];
      }
    ?>
   
    <div class="video-card" onclick="openModal('<?= htmlspecialchars($video_src) ?>')">
      <?php if ($video_src && strpos($video_src, 'uploads/') === 0): ?>
        <video class="video-thumb"
               muted
               preload="auto"
               playsinline
               controlsList="nodownload"
               oncontextmenu="return false"
               tabindex="-1"
               >
          <source src="<?= htmlspecialchars($video_src) ?>" type="video/mp4" />
          متصفحك لا يدعم عرض الفيديو.
        </video>
      <?php elseif($video_src): ?>
        <a class="watch-link" href="<?= htmlspecialchars($video_src) ?>" target="_blank" rel="noopener noreferrer">مشاهدة الفيديو من الرابط</a>
      <?php else: ?>
        <div class="video-thumb" style="display:flex; justify-content:center; align-items:center; color:#999; font-size:14px;">
          لا يوجد فيديو
        </div>
      <?php endif; ?>

      <div class="video-info">
        <div class="video-title"><?= htmlspecialchars($video['title']) ?></div>
        <div class="video-meta">
          المرحلة: <strong><?= htmlspecialchars($video['level']) ?></strong> |
          التصنيف: <?= htmlspecialchars($video['category']) ?> |
          التاريخ: <?= htmlspecialchars($video['uploaded_at']) ?>
        </div>
        <div class="video-description"><?= nl2br(htmlspecialchars($video['description'])) ?></div>
      </div>
    </div>
  <?php endwhile; ?>

</div>

<!-- مودال الفيديو -->
<div id="videoModal" class="modal" onclick="closeModal(event)" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
  <div class="modal-content">
    <span class="close-btn" onclick="closeModal(event)" aria-label="إغلاق">&times;</span>
    <video id="modalVideo" controls autoplay controlsList="nodownload" oncontextmenu="return false" preload="auto" playsinline></video>
  </div>
</div>

<script>
  function openModal(videoSrc) {
    if(!videoSrc) return;
    const modal = document.getElementById('videoModal');
    const modalVideo = document.getElementById('modalVideo');
    modalVideo.pause();
    modalVideo.removeAttribute('src');
    modalVideo.load();

    modalVideo.src = videoSrc;
    modal.style.display = 'flex';
    modalVideo.play();
  }

  function closeModal(event) {
    const modal = document.getElementById('videoModal');
    const modalVideo = document.getElementById('modalVideo');
    if (event.target === modal || event.target.classList.contains('close-btn')) {
      modal.style.display = 'none';
      modalVideo.pause();
      modalVideo.removeAttribute('src');
      modalVideo.load();
    }
  }

  // غلق المودال بالضغط على Escape
  document.addEventListener('keydown', (e) => {
    if(e.key === "Escape") {
      const modal = document.getElementById('videoModal');
      if(modal.style.display === 'flex') {
        modal.style.display = 'none';
        const modalVideo = document.getElementById('modalVideo');
        modalVideo.pause();
        modalVideo.removeAttribute('src');
        modalVideo.load();
      }
    }
  });
</script>

</body>
</html>
