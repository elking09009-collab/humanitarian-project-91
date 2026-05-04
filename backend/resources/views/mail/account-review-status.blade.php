<!DOCTYPE html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <title>تحديث حالة الحساب</title>
</head>
<body style="font-family:Tahoma,Arial,sans-serif;line-height:1.8;">
    <h2>مرحباً {{ $user->name }}</h2>

    @if ($status === 'approved')
        <p>تم اعتماد حسابك ويمكنك الآن تسجيل الدخول.</p>
    @else
        <p>تم رفض طلب إنشاء الحساب.</p>
        @if (!empty($reason))
            <p><strong>سبب الرفض:</strong> {{ $reason }}</p>
        @endif
    @endif

    <p>يمكنك متابعة حالة الطلب من صفحة حالة الطلب في المنصة.</p>
</body>
</html>
