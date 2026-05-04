<?php

namespace App\Services;

use Illuminate\Support\Facades\DB;

class NeedsPredictorService
{
    public function predictNextMonth(): array
    {
        $driver = DB::connection()->getDriverName();
        $monthExpr = $driver === 'sqlite'
            ? "strftime('%Y-%m', created_at)"
            : "DATE_FORMAT(created_at, '%Y-%m')";

        $rows = DB::table('needs')
            ->selectRaw("area_id, type, {$monthExpr} as month_key, COUNT(*) as total")
            ->groupBy('area_id', 'type', 'month_key')
            ->orderBy('area_id')
            ->orderBy('type')
            ->orderBy('month_key')
            ->get();

        $groups = [];

        foreach ($rows as $row) {
            $key = $row->area_id . '|' . $row->type;
            $groups[$key][] = [
                'month' => $row->month_key,
                'total' => (int) $row->total,
                'area_id' => $row->area_id,
                'type' => $row->type,
            ];
        }

        $predictions = [];

        foreach ($groups as $series) {
            $n = count($series);
            if ($n === 0) {
                continue;
            }

            $x = range(1, $n);
            $y = array_column($series, 'total');

            $sumX = array_sum($x);
            $sumY = array_sum($y);
            $sumXY = 0;
            $sumX2 = 0;

            for ($i = 0; $i < $n; $i++) {
                $sumXY += $x[$i] * $y[$i];
                $sumX2 += $x[$i] * $x[$i];
            }

            $denominator = ($n * $sumX2) - ($sumX * $sumX);
            $slope = $denominator !== 0 ? (($n * $sumXY) - ($sumX * $sumY)) / $denominator : 0;
            $last = $y[$n - 1];
            $prediction = max(0, (int) round($last + $slope));

            $predictions[] = [
                'area_id' => $series[0]['area_id'],
                'type' => $series[0]['type'],
                'months' => $n,
                'last_value' => $last,
                'trend_slope' => round($slope, 2),
                'predicted_next_month' => $prediction,
            ];
        }

        usort($predictions, fn ($a, $b) => $b['predicted_next_month'] <=> $a['predicted_next_month']);

        return $predictions;
    }
}
