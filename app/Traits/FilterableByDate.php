<?php

namespace App\Traits;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

trait FilterableByDate
{
    /**
     * Parse date filter from request and return start/end date strings and label.
     */
    public function resolveDateFilter(Request $request): array
    {
        $range = $request->get('date_range', 'this_month');
        $startDate = null;
        $endDate = null;
        $label = 'Bulan Ini';

        switch ($range) {
            case 'today':
                $startDate = Carbon::today()->startOfDay();
                $endDate = Carbon::today()->endOfDay();
                $label = 'Hari Ini (' . Carbon::today()->format('d M Y') . ')';
                break;

            case 'this_week':
                $startDate = Carbon::now()->startOfWeek();
                $endDate = Carbon::now()->endOfWeek();
                $label = 'Minggu Ini (' . $startDate->format('d M') . ' - ' . $endDate->format('d M Y') . ')';
                break;

            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $label = 'Bulan Ini (' . Carbon::now()->format('F Y') . ')';
                break;

            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                $endDate = Carbon::now()->endOfYear();
                $label = 'Tahun Ini (' . Carbon::now()->format('Y') . ')';
                break;

            case 'all_time':
                $startDate = null;
                $endDate = null;
                $label = 'Semua Waktu';
                break;

            case 'custom':
                if ($request->filled('start_date') && $request->filled('end_date')) {
                    $startDate = Carbon::parse($request->start_date)->startOfDay();
                    $endDate = Carbon::parse($request->end_date)->endOfDay();
                    $label = $startDate->format('d M Y') . ' - ' . $endDate->format('d M Y');
                } else {
                    $range = 'this_month';
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    $label = 'Bulan Ini (' . Carbon::now()->format('F Y') . ')';
                }
                break;

            default:
                $range = 'this_month';
                $startDate = Carbon::now()->startOfMonth();
                $endDate = Carbon::now()->endOfMonth();
                $label = 'Bulan Ini (' . Carbon::now()->format('F Y') . ')';
                break;
        }

        return [
            'range'      => $range,
            'start_date' => $startDate,
            'end_date'   => $endDate,
            'label'      => $label,
            'raw_start'  => $request->get('start_date', ''),
            'raw_end'    => $request->get('end_date', ''),
        ];
    }

    /**
     * Apply date range filter to an Eloquent/Query builder.
     */
    public function applyDateFilter($query, array $dateFilter, string $column = 'created_at')
    {
        if (!empty($dateFilter['start_date']) && !empty($dateFilter['end_date'])) {
            $query->whereBetween($column, [$dateFilter['start_date'], $dateFilter['end_date']]);
        }
        return $query;
    }

    /**
     * Generate dynamic chart intervals based on the active date filter.
     */
    public function generateChartTrendData(array $dateFilter, $orderQueryBuilder, $cashFlowQueryBuilder): array
    {
        $range = $dateFilter['range'];
        $labels = [];
        $revenueData = [];
        $expenseData = [];

        if ($range === 'today') {
            // Group by 24 Hours
            for ($hour = 0; $hour < 24; $hour++) {
                $labels[] = sprintf('%02d:00', $hour);
                $start = Carbon::today()->setHour($hour)->setMinute(0)->setSecond(0);
                $end   = Carbon::today()->setHour($hour)->setMinute(59)->setSecond(59);

                $revenueData[] = (float) (clone $orderQueryBuilder)->whereBetween('created_at', [$start, $end])->sum('total_amount');
                $expenseData[] = (float) (clone $cashFlowQueryBuilder)->whereBetween('created_at', [$start, $end])->sum('amount');
            }
        } elseif ($range === 'this_week') {
            // Group by Day of Week (Mon - Sun)
            $start = Carbon::now()->startOfWeek();
            for ($i = 0; $i < 7; $i++) {
                $currentDay = (clone $start)->addDays($i);
                $labels[] = $currentDay->translatedFormat('D, d M');
                
                $dayStart = (clone $currentDay)->startOfDay();
                $dayEnd   = (clone $currentDay)->endOfDay();

                $revenueData[] = (float) (clone $orderQueryBuilder)->whereBetween('created_at', [$dayStart, $dayEnd])->sum('total_amount');
                $expenseData[] = (float) (clone $cashFlowQueryBuilder)->whereBetween('created_at', [$dayStart, $dayEnd])->sum('amount');
            }
        } elseif ($range === 'this_month') {
            // Group by Days in current Month
            $start = Carbon::now()->startOfMonth();
            $daysInMonth = $start->daysInMonth;
            
            for ($day = 1; $day <= $daysInMonth; $day++) {
                $currentDay = (clone $start)->setDay($day);
                $labels[] = $currentDay->format('d M');
                
                $dayStart = (clone $currentDay)->startOfDay();
                $dayEnd   = (clone $currentDay)->endOfDay();

                $revenueData[] = (float) (clone $orderQueryBuilder)->whereBetween('created_at', [$dayStart, $dayEnd])->sum('total_amount');
                $expenseData[] = (float) (clone $cashFlowQueryBuilder)->whereBetween('created_at', [$dayStart, $dayEnd])->sum('amount');
            }
        } elseif ($range === 'this_year') {
            // Group by 12 Months
            for ($month = 1; $month <= 12; $month++) {
                $start = Carbon::now()->setMonth($month)->startOfMonth();
                $end   = Carbon::now()->setMonth($month)->endOfMonth();
                $labels[] = $start->format('M Y');

                $revenueData[] = (float) (clone $orderQueryBuilder)->whereBetween('created_at', [$start, $end])->sum('total_amount');
                $expenseData[] = (float) (clone $cashFlowQueryBuilder)->whereBetween('created_at', [$start, $end])->sum('amount');
            }
        } elseif ($range === 'custom') {
            $startDate = $dateFilter['start_date'];
            $endDate   = $dateFilter['end_date'];
            $diffInDays = $startDate->diffInDays($endDate);

            if ($diffInDays <= 31) {
                // Day by day
                $curr = clone $startDate;
                while ($curr->lte($endDate)) {
                    $labels[] = $curr->format('d M');
                    $dayStart = (clone $curr)->startOfDay();
                    $dayEnd   = (clone $curr)->endOfDay();

                    $revenueData[] = (float) (clone $orderQueryBuilder)->whereBetween('created_at', [$dayStart, $dayEnd])->sum('total_amount');
                    $expenseData[] = (float) (clone $cashFlowQueryBuilder)->whereBetween('created_at', [$dayStart, $dayEnd])->sum('amount');

                    $curr->addDay();
                }
            } else {
                // Month by month
                $curr = (clone $startDate)->startOfMonth();
                while ($curr->lte($endDate)) {
                    $labels[] = $curr->format('M Y');
                    $monthStart = (clone $curr)->startOfMonth();
                    $monthEnd   = (clone $curr)->endOfMonth();

                    $revenueData[] = (float) (clone $orderQueryBuilder)->whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_amount');
                    $expenseData[] = (float) (clone $cashFlowQueryBuilder)->whereBetween('created_at', [$monthStart, $monthEnd])->sum('amount');

                    $curr->addMonth();
                }
            }
        } else {
            // All time -> Group by last 6 months or all months
            $curr = Carbon::now()->subMonths(5)->startOfMonth();
            for ($i = 0; $i < 6; $i++) {
                $monthStart = (clone $curr)->startOfMonth();
                $monthEnd   = (clone $curr)->endOfMonth();
                $labels[] = $monthStart->format('M Y');

                $revenueData[] = (float) (clone $orderQueryBuilder)->whereBetween('created_at', [$monthStart, $monthEnd])->sum('total_amount');
                $expenseData[] = (float) (clone $cashFlowQueryBuilder)->whereBetween('created_at', [$monthStart, $monthEnd])->sum('amount');

                $curr->addMonth();
            }
        }

        return [
            'labels'   => $labels,
            'revenues' => $revenueData,
            'expenses' => $expenseData,
        ];
    }
}
