<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ActivitySubmission;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class ProgressController extends Controller
{
    public function getMonthlyProgress()
    {
        $user = Auth::user();

        // Get count of activities completed per month for last 6 months
        $data = ActivitySubmission::select(
                DB::raw('YEAR(created_at) as year'),
                DB::raw('MONTH(created_at) as month'),
                DB::raw('COUNT(*) as count')
            )
            ->where('user_id', $user->id)
            ->where('created_at', '>=', Carbon::now()->subMonths(5)->startOfMonth())
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get();

        // Prepare labels and data arrays
        $labels = [];
        $counts = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = Carbon::now()->subMonths($i);
            $labels[] = $date->format('F Y');
            $monthData = $data->firstWhere(function ($item) use ($date) {
                return $item->year == $date->year && $item->month == $date->month;
            });
            $counts[] = $monthData ? $monthData->count : 0;
        }

        return response()->json([
            'labels' => $labels,
            'data' => $counts,
        ]);
    }

    public function showProgress()
    {
        return view('progress');
    }
}
