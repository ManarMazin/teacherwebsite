<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>دورات تعلم اللغة الإنجليزية</title>
  <link href="https://fonts.googleapis.com/css2?family=Cairo:wght@400;700&display=swap" rel="stylesheet">
  <style>
    body {
      font-family: 'Cairo', sans-serif;
      margin: 0;
      background: #f5f8ff;
      color: #333;
    }

    header {
      background: linear-gradient(135deg, #3f51b5, #673ab7);
      color: white;
      padding: 60px 20px;
      text-align: center;
    }

    header h1 {
      font-size: 36px;
      margin-bottom: 15px;
    }

    header p {
      font-size: 18px;
      margin-bottom: 30px;
    }

    .btn {
      background-color: #ff9800;
      color: white;
      padding: 14px 28px;
      font-size: 18px;
      border: none;
      border-radius: 6px;
      cursor: pointer;
      transition: 0.3s ease;
      text-decoration: none;
    }

    .btn:hover {
      background-color: #e68900;
    }

    section {
      padding: 60px 20px;
      max-width: 1000px;
      margin: auto;
    }

    section h2 {
      font-size: 28px;
      color: #3f51b5;
      margin-bottom: 20px;
      border-bottom: 2px solid #3f51b5;
      display: inline-block;
      padding-bottom: 5px;
    }

    section p {
      font-size: 18px;
      line-height: 1.7;
    }

    .lessons ul {
      list-style: none;
      padding: 0;
      margin-top: 30px;
    }

    .lessons li {
      background-color: #fff;
      margin-bottom: 15px;
      padding: 15px 20px;
      border-radius: 8px;
      box-shadow: 0 2px 5px rgba(0,0,0,0.06);
      font-size: 16px;
      transition: transform 0.3s ease;
    }

    .lessons li:hover {
      transform: translateX(-5px);
    }

    .contact-form input, .contact-form textarea {
      width: 100%;
      padding: 12px;
      margin-top: 10px;
      border: 1px solid #ccc;
      border-radius: 6px;
      font-size: 16px;
    }

    .contact-form button {
      margin-top: 15px;
      background-color: #3f51b5;
      color: white;
      padding: 12px 24px;
      border: none;
      font-size: 18px;
      border-radius: 6px;
      cursor: pointer;
    }

    .illustration {
      max-width: 300px;
      margin: 30px auto;
      display: block;
      animation: float 4s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0); }
      50% { transform: translateY(-10px); }
    }

    footer {
      text-align: center;
      padding: 30px;
      background: #3f51b5;
      color: white;
    }
  </style>
</head>
<body>

<header>
  <h1>التسجيل في دورات اللغة الإنجليزية</h1>
  <p>انضم لنا وتعلم بأسلوب ممتع ومبسط. سجّل الآن وابدأ رحلتك التعليمية.</p>
  <a href="#register" class="btn">سجّل الآن</a>
</header>

<section class="intro">
  <img src="https://cdn-icons-png.flaticon.com/512/3135/3135715.png" alt="معلم لغة إنجليزية" class="illustration" />
  <h2>من نحن</h2>
  <p>نحن فريق تعليمي متخصص يقدم دروساً في اللغة الإنجليزية بطريقة تفاعلية، عبر الإنترنت، وبأسلوب بسيط يناسب الجميع.</p>
</section>

<section class="lessons" id="lessons">
  <h2>الدروس المتاحة</h2>
  <ul>
    <li>محادثات يومية باللغة الإنجليزية</li>
    <li>شرح القواعد النحوية بطريقة مبسطة</li>
    <li>تمارين استماع وفهم للمبتدئين</li>
    <li>مهارات الكتابة العملية والأكاديمية</li>
  </ul>
</section>

<section class="contact" id="contact">
  <h2>تواصل معنا</h2>
  <p>هل لديك سؤال أو استفسار؟ أرسل لنا رسالتك وسنرد عليك بأقرب وقت.</p>
  <form class="contact-form">
    <input type="text" placeholder="الاسم الكامل" required />
    <input type="email" placeholder="البريد الإلكتروني" required />
    <textarea rows="4" placeholder="اكتب رسالتك هنا" required></textarea>
    <button type="submit">إرسال</button>
  </form>
</section>

<footer>
  جميع الحقوق محفوظة © 2025 - موقع تعليم اللغة الإنجليزية
</footer>

</body>
</html>

