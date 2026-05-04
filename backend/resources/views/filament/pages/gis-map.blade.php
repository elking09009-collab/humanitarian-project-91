<x-filament-panels::page>
    <div class="space-y-4">
        {{-- Summary Stats --}}
        <div class="grid grid-cols-3 gap-4">
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center border border-gray-200 dark:border-gray-700">
                <p class="text-2xl font-bold text-amber-600" id="total-areas">--</p>
                <p class="text-sm text-gray-500">إجمالي المناطق</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center border border-gray-200 dark:border-gray-700">
                <p class="text-2xl font-bold text-orange-500" id="total-pending">--</p>
                <p class="text-sm text-gray-500">احتياجات معلقة</p>
            </div>
            <div class="bg-white dark:bg-gray-800 rounded-xl shadow p-4 text-center border border-gray-200 dark:border-gray-700">
                <p class="text-2xl font-bold text-green-500" id="total-delivered">--</p>
                <p class="text-sm text-gray-500">تم إيصالها</p>
            </div>
        </div>

        {{-- Legend --}}
        <div class="flex gap-4 items-center text-sm bg-white dark:bg-gray-800 rounded-xl p-3 border border-gray-200 dark:border-gray-700">
            <span class="font-semibold text-gray-600 dark:text-gray-300">مستوى الأولوية:</span>
            <span class="flex items-center gap-1"><span class="inline-block w-4 h-4 rounded-full bg-red-500"></span> عالية</span>
            <span class="flex items-center gap-1"><span class="inline-block w-4 h-4 rounded-full bg-amber-500"></span> متوسطة</span>
            <span class="flex items-center gap-1"><span class="inline-block w-4 h-4 rounded-full bg-green-500"></span> منخفضة</span>
        </div>

        {{-- Map --}}
        <div class="bg-white dark:bg-gray-800 rounded-xl shadow border border-gray-200 dark:border-gray-700 overflow-hidden">
            <div id="htr-map" style="height: 520px; width: 100%;"></div>
        </div>
    </div>

    {{-- Leaflet CDN --}}
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script type="application/json" id="areas-data-json">{!! json_encode($this->getAreasData(), JSON_UNESCAPED_UNICODE) !!}</script>

    <script>
        const areasDataEl = document.getElementById('areas-data-json');
        const areasData = areasDataEl ? JSON.parse(areasDataEl.textContent || '[]') : [];

        document.addEventListener('DOMContentLoaded', function () {
            const map = L.map('htr-map').setView([23.8859, 45.0792], 5);

            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
            }).addTo(map);

            let totalPending = 0, totalDelivered = 0;

            areasData.forEach(area => {
                totalPending   += area.pending;
                totalDelivered += area.delivered;

                const color = area.priority === 'high'   ? '#ef4444' :
                              area.priority === 'medium' ? '#f59e0b' : '#22c55e';

                const radius = 8 + Math.min(area.needs_count * 3, 30);

                const circle = L.circleMarker([area.lat, area.lng], {
                    radius:      radius,
                    fillColor:   color,
                    color:       '#fff',
                    weight:      2,
                    opacity:     1,
                    fillOpacity: 0.85
                }).addTo(map);

                circle.bindPopup(`
                    <div dir="rtl" style="min-width:180px; font-family:sans-serif;">
                        <h3 style="margin:0 0 8px; font-size:15px; font-weight:700;">${area.name}</h3>
                        <p style="margin:2px 0;">📦 إجمالي الاحتياجات: <strong>${area.needs_count}</strong></p>
                        <p style="margin:2px 0;">⏳ معلقة: <strong style="color:#f59e0b">${area.pending}</strong></p>
                        <p style="margin:2px 0;">✅ تم إيصالها: <strong style="color:#22c55e">${area.delivered}</strong></p>
                        <p style="margin:2px 0;">🎯 الأولوية: <strong>${area.priority}</strong></p>
                        <p style="margin:2px 0;">📍 الحالة: <strong>${area.status}</strong></p>
                        <a href="/admin/areas/${area.id}/edit" style="display:block;margin-top:8px;color:#f59e0b;font-size:13px;">✏️ تعديل المنطقة</a>
                    </div>
                `);

                circle.bindTooltip(area.name, { permanent: false, direction: 'top' });
            });

            document.getElementById('total-areas').textContent     = areasData.length;
            document.getElementById('total-pending').textContent   = totalPending;
            document.getElementById('total-delivered').textContent = totalDelivered;
        });
    </script>
</x-filament-panels::page>
