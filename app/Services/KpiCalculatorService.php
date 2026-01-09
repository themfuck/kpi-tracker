<?php

namespace App\Services;

use App\Models\Host;
use App\Models\KpiTarget;
use App\Models\LiveSession;
use Illuminate\Support\Facades\DB;

class KpiCalculatorService
{
    protected $target;

    public function __construct()
    {
        $this->target = KpiTarget::first() ?? $this->getDefaultTarget();
    }

    protected function getDefaultTarget()
    {
        return (object) [
            'gmv_per_hour' => 2700000,
            'conversion_rate' => 0.03,
            'aov' => 180000,
            'likes_per_minute' => 300,
        ];
    }

    /**
     * Calculate GMV per hour for a host
     */
    public function calculateGmvPerHour($totalGmv, $totalHours)
    {
        if ($totalHours == 0) return 0;
        return $totalGmv / $totalHours;
    }

    /**
     * Calculate Conversion Rate
     */
    public function calculateConversionRate($orders, $viewers)
    {
        if ($viewers == 0) return 0;
        return $orders / $viewers;
    }

    /**
     * Calculate Average Order Value (AOV)
     */
    public function calculateAov($gmv, $orders)
    {
        if ($orders == 0) return 0;
        return $gmv / $orders;
    }

    /**
     * Calculate Likes per minute
     */
    public function calculateLikesPerMinute($likes, $hours)
    {
        if ($hours == 0) return 0;
        $minutes = $hours * 60;
        return $likes / $minutes;
    }

    /**
     * Calculate host score (0-100) based on weighted KPIs
     * Bobot:
     * - GMV per jam → 30%
     * - Conversion Rate → 20%
     * - AOV → 15%
     * - Like per menit → 10%
     * - Total GMV bulanan → 25%
     */
    public function calculateHostScore(Host $host, $month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $sessions = $host->liveSessions()
            ->whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        if ($sessions->isEmpty()) {
            return 0;
        }

        $totalGmv = $sessions->sum('gmv');
        $totalHours = $sessions->sum('hours_live');
        $totalOrders = $sessions->sum('orders');
        $totalViewers = $sessions->sum('viewers');
        $totalLikes = $sessions->sum('likes');

        // Calculate KPIs
        $gmvPerHour = $this->calculateGmvPerHour($totalGmv, $totalHours);
        $conversionRate = $this->calculateConversionRate($totalOrders, $totalViewers);
        $aov = $this->calculateAov($totalGmv, $totalOrders);
        $likesPerMinute = $this->calculateLikesPerMinute($totalLikes, $totalHours);

        // Calculate percentage achievement vs target
        $gmvPerHourScore = min(100, ($gmvPerHour / $this->target->gmv_per_hour) * 100);
        $conversionRateScore = min(100, ($conversionRate / $this->target->conversion_rate) * 100);
        $aovScore = min(100, ($aov / $this->target->aov) * 100);
        $likesPerMinuteScore = min(100, ($likesPerMinute / $this->target->likes_per_minute) * 100);
        
        // Total GMV score (target 1.3M per month)
        $monthlyGmvTarget = 1300000000;
        $totalGmvScore = min(100, ($totalGmv / $monthlyGmvTarget) * 100);

        // Weighted score
        $score = (
            ($gmvPerHourScore * 0.30) +
            ($conversionRateScore * 0.20) +
            ($aovScore * 0.15) +
            ($likesPerMinuteScore * 0.10) +
            ($totalGmvScore * 0.25)
        );

        return round($score, 2);
    }

    /**
     * Get KPI status based on achievement percentage
     * OK → ≥ 100% target (Hijau)
     * WARNING → ≥ 80% target (Kuning)
     * DROP → < 80% target (Merah)
     */
    public function getKpiStatus($score)
    {
        if ($score >= 100) {
            return 'OK';
        } elseif ($score >= 80) {
            return 'WARNING';
        } else {
            return 'DROP';
        }
    }

    /**
     * Get status color for badges
     */
    public function getStatusColor($status)
    {
        return match($status) {
            'OK' => 'success',
            'WARNING' => 'warning',
            'DROP' => 'danger',
            default => 'gray',
        };
    }

    /**
     * Get host rankings for a specific month
     */
    public function getHostRankings($month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $hosts = Host::where('is_active', true)->get();
        
        $rankings = $hosts->map(function ($host) use ($month, $year) {
            $sessions = $host->liveSessions()
                ->whereMonth('date', $month)
                ->whereYear('date', $year)
                ->get();

            $totalGmv = $sessions->sum('gmv');
            $totalHours = $sessions->sum('hours_live');
            $totalOrders = $sessions->sum('orders');
            $totalViewers = $sessions->sum('viewers');
            $totalLikes = $sessions->sum('likes');

            $score = $this->calculateHostScore($host, $month, $year);
            $status = $this->getKpiStatus($score);

            return [
                'host' => $host,
                'score' => $score,
                'status' => $status,
                'total_gmv' => $totalGmv,
                'total_hours' => $totalHours,
                'gmv_per_hour' => $this->calculateGmvPerHour($totalGmv, $totalHours),
                'conversion_rate' => $this->calculateConversionRate($totalOrders, $totalViewers),
                'aov' => $this->calculateAov($totalGmv, $totalOrders),
                'likes_per_minute' => $this->calculateLikesPerMinute($totalLikes, $totalHours),
            ];
        });

        // Sort by score DESC
        return $rankings->sortByDesc('score')->values();
    }

    /**
     * Get monthly GMV statistics
     */
    public function getMonthlyStats($month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        $sessions = LiveSession::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->get();

        $totalGmv = $sessions->sum('gmv');
        $totalHours = $sessions->sum('hours_live');
        $avgGmvPerHour = $this->calculateGmvPerHour($totalGmv, $totalHours);
        
        $monthlyTarget = 1300000000;
        $achievement = ($totalGmv / $monthlyTarget) * 100;

        return [
            'total_gmv' => $totalGmv,
            'total_hours' => $totalHours,
            'avg_gmv_per_hour' => $avgGmvPerHour,
            'achievement_percentage' => round($achievement, 2),
            'monthly_target' => $monthlyTarget,
        ];
    }

    /**
     * Get daily GMV for chart
     */
    public function getDailyGmv($month = null, $year = null)
    {
        $month = $month ?? now()->month;
        $year = $year ?? now()->year;

        return LiveSession::whereMonth('date', $month)
            ->whereYear('date', $year)
            ->select(
                DB::raw('DATE(date) as date'),
                DB::raw('SUM(gmv) as total_gmv')
            )
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->pluck('total_gmv', 'date')
            ->toArray();
    }
}
