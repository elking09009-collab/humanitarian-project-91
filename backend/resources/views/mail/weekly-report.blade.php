<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: Tahoma, Arial, sans-serif; color: #1f2937; }
        .card { border: 1px solid #e5e7eb; border-radius: 10px; padding: 16px; margin-bottom: 12px; }
        .title { font-size: 20px; font-weight: bold; margin-bottom: 14px; }
        .grid { width: 100%; border-collapse: collapse; }
        .grid th, .grid td { border: 1px solid #e5e7eb; padding: 8px; text-align: right; }
        .muted { color: #6b7280; font-size: 13px; }
    </style>
</head>
<body>
    <div class="title">التقرير الأسبوعي لمنصة الدعم الإنساني</div>

    <div class="card">
        <div><strong>الفترة:</strong> {{ $stats['from']->format('Y-m-d H:i') }} - {{ $stats['to']->format('Y-m-d H:i') }}</div>
        <div class="muted">تم إنشاء هذا التقرير تلقائياً بواسطة النظام.</div>
    </div>

    <table class="grid">
        <tr><th>المؤشر</th><th>القيمة</th></tr>
        <tr><td>إجمالي المستخدمين</td><td>{{ $stats['users_total'] }}</td></tr>
        <tr><td>مستخدمون جدد (7 أيام)</td><td>{{ $stats['users_new'] }}</td></tr>
        <tr><td>حسابات بانتظار المراجعة</td><td>{{ $stats['users_pending'] }}</td></tr>
        <tr><td>إجمالي الاحتياجات</td><td>{{ $stats['needs_total'] }}</td></tr>
        <tr><td>احتياجات جديدة (7 أيام)</td><td>{{ $stats['needs_new'] }}</td></tr>
        <tr><td>احتياجات pending</td><td>{{ $stats['needs_pending'] }}</td></tr>
        <tr><td>احتياجات delivered</td><td>{{ $stats['needs_delivered'] }}</td></tr>
        <tr><td>موافقات الحسابات (7 أيام)</td><td>{{ $stats['reviews_approved'] }}</td></tr>
        <tr><td>رفض الحسابات (7 أيام)</td><td>{{ $stats['reviews_rejected'] }}</td></tr>
    </table>

    <div class="card" style="margin-top: 12px;">
        <strong>الاحتياجات حسب النوع:</strong>
        <ul>
            @forelse($stats['needs_by_type'] as $row)
                <li>{{ $row->type }}: {{ $row->total }}</li>
            @empty
                <li>لا توجد بيانات</li>
            @endforelse
        </ul>
    </div>
</body>
</html>
