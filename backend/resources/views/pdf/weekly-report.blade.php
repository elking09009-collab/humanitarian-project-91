<!doctype html>
<html lang="ar" dir="rtl">
<head>
    <meta charset="UTF-8">
    <style>
        body { font-family: DejaVu Sans, sans-serif; color: #111827; font-size: 13px; }
        h1 { font-size: 20px; margin-bottom: 8px; }
        .small { color: #6b7280; margin-bottom: 12px; }
        table { width: 100%; border-collapse: collapse; margin-bottom: 12px; }
        th, td { border: 1px solid #d1d5db; padding: 8px; text-align: right; }
        th { background: #f3f4f6; }
    </style>
</head>
<body>
    <h1>التقرير الأسبوعي لمنصة الدعم الإنساني</h1>
    <div class="small">الفترة: {{ $stats['from']->format('Y-m-d H:i') }} - {{ $stats['to']->format('Y-m-d H:i') }}</div>

    <table>
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

    <table>
        <tr><th>نوع الاحتياج</th><th>العدد</th></tr>
        @forelse($stats['needs_by_type'] as $row)
            <tr>
                <td>{{ $row->type }}</td>
                <td>{{ $row->total }}</td>
            </tr>
        @empty
            <tr><td colspan="2">لا توجد بيانات</td></tr>
        @endforelse
    </table>
</body>
</html>
