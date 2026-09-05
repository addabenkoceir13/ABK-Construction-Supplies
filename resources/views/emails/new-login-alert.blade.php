<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>تنبيه أمان - تسجيل دخول جديد</title>
  <style>
    body {
      margin: 0;
      padding: 0;
      background-color: #0f172a;
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      color: #e2e8f0;
      direction: rtl;
      text-align: right;
    }
    .email-container {
      max-width: 600px;
      margin: 30px auto;
      background: #1e293b;
      border-radius: 18px;
      overflow: hidden;
      box-shadow: 0 15px 35px rgba(0,0,0,0.5);
      border: 1px solid rgba(255, 255, 255, 0.1);
    }
    .email-header {
      background: linear-gradient(135deg, #696cff 0%, #4338ca 100%);
      padding: 30px 25px;
      text-align: center;
      color: #ffffff;
    }
    .header-badge {
      display: inline-block;
      width: 60px;
      height: 60px;
      line-height: 60px;
      background: rgba(255, 255, 255, 0.2);
      border-radius: 50%;
      font-size: 28px;
      margin-bottom: 12px;
    }
    .email-title {
      font-size: 22px;
      font-weight: 800;
      margin: 0 0 6px 0;
    }
    .email-subtitle {
      font-size: 14px;
      opacity: 0.9;
      margin: 0;
    }
    .email-body {
      padding: 30px 25px;
    }
    .greeting {
      font-size: 17px;
      font-weight: 700;
      color: #ffffff;
      margin-bottom: 15px;
    }
    .message-text {
      font-size: 14px;
      line-height: 1.7;
      color: #94a3b8;
      margin-bottom: 25px;
    }
    .details-card {
      background: #0f172a;
      border-radius: 12px;
      padding: 20px;
      border: 1px solid rgba(255, 255, 255, 0.08);
      margin-bottom: 25px;
    }
    .detail-row {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 10px 0;
      border-bottom: 1px solid rgba(255, 255, 255, 0.06);
      font-size: 13.5px;
    }
    .detail-row:last-child {
      border-bottom: none;
    }
    .detail-label {
      color: #64748b;
      font-weight: 600;
    }
    .detail-value {
      color: #f1f5f9;
      font-weight: 700;
      direction: ltr;
      text-align: left;
    }
    .warning-box {
      background: rgba(255, 171, 0, 0.1);
      border: 1px solid rgba(255, 171, 0, 0.35);
      border-radius: 10px;
      padding: 14px 18px;
      font-size: 13px;
      color: #ffab00;
      line-height: 1.6;
      margin-bottom: 25px;
    }
    .action-button {
      display: block;
      width: 100%;
      box-sizing: border-box;
      text-align: center;
      background: linear-gradient(135deg, #696cff 0%, #4f46e5 100%);
      color: #ffffff !important;
      text-decoration: none;
      padding: 14px 20px;
      border-radius: 10px;
      font-weight: 700;
      font-size: 15px;
      box-shadow: 0 6px 18px rgba(105, 108, 255, 0.35);
    }
    .email-footer {
      background: #0f172a;
      padding: 20px;
      text-align: center;
      font-size: 12px;
      color: #64748b;
      border-top: 1px solid rgba(255, 255, 255, 0.06);
    }
  </style>
</head>
<body>
  <div class="email-container">
    <div class="email-header">
      <div class="header-badge">🛡️</div>
      <h1 class="email-title">تنبيه أمان: تسجيل دخول جديد</h1>
      <p class="email-subtitle">A.B.K Construction Supplies • عدة بن قصير سفيان</p>
    </div>

    <div class="email-body">
      <div class="greeting">مرحباً، {{ $user->display_name ?? $user->name }}</div>
      <p class="message-text">
        تم رصد عملية تسجيل دخول ناجحة جديدة إلى حسابك في نظام <strong>A.B.K لمستلزمات البناء</strong>. فيما يلي تفاصيل جلسة تسجيل الدخول:
      </p>

      <div class="details-card">
        <div class="detail-row">
          <span class="detail-label">البريد الإلكتروني:</span>
          <span class="detail-value">{{ $user->email }}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">عنوان IP:</span>
          <span class="detail-value">{{ $loginData['ip'] ?? 'Unknown' }}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">الوقت والتاريخ:</span>
          <span class="detail-value">{{ $loginData['time'] ?? now()->format('Y-m-d H:i:s') }}</span>
        </div>
        <div class="detail-row">
          <span class="detail-label">الجهاز / المتصفح:</span>
          <span class="detail-value" style="max-width: 280px; word-break: break-all;">{{ Str::limit($loginData['user_agent'] ?? 'Web Browser', 80) }}</span>
        </div>
      </div>

      <div class="warning-box">
        ⚠️ <strong>هل كنت أنت من قام بهذه العملية؟</strong><br>
        إذا كنت أنت، يمكنك تجاهل هذه الرسالة بأمان. أما إذا لم تكن أنت أو تشك في أي نشاط مريب، يرجى تغيير كلمة المرور فوراً لتأمين حسابك.
      </div>

      <a href="{{ url('auth/forgot-password-basic') }}" class="action-button">
        تأمين الحساب وتغيير كلمة المرور
      </a>
    </div>

    <div class="email-footer">
      هذا البريد تم إرساله تلقائياً من خادم الأمان في مؤسسة عدة بن قصير سفيان • A.B.K Construction Supplies<br>
      © {{ date('Y') }} جميع الحقوق محفوظة.
    </div>
  </div>
</body>
</html>
