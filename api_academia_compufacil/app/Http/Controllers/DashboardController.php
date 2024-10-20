<?php

namespace App\Http\Controllers;
use PDF;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
class DashboardController extends Controller
{
    //
    public function getDashboardStats()
    {
        $stats = [
            'app_users' => $this->getAppUsers(),
            'active_courses' => $this->getActiveCourses(),
            'platform_students' => $this->getPlatformStudents(),
            'sold_courses' => $this->getSoldCourses(),

            'monthly_sales' => $this->getMonthlySales(),
            'total_revenue' => $this->getTotalRevenue(),
        ];

        return response()->json($stats);
    }

    private function getAppUsers()
    {
        return DB::table('users')
            ->whereNotNull('role_id')
            ->count();
    }

    private function getActiveCourses()
    {
        return DB::table('courses')
            ->where('state', 2)
            ->count();
    }

    private function getPlatformStudents()
    {
        return DB::table('users')
            ->whereNull('role_id')
            ->count();
    }

    private function getSoldCourses()
    {
        return DB::table('sale_details')
            ->count();
    }

    private function getMonthlySales()
    {
        $sixMonthsAgo = Carbon::now()->subMonths(5)->startOfMonth();
        $months = [];
    
        // Generar los últimos 6 meses
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i)->format('M');
            $months[$month] = 0;
        }
    
        $sales = DB::table('sales')
            ->select(DB::raw('DATE_FORMAT(created_at, "%b") as month'), DB::raw('SUM(total) as total'))
            ->where('created_at', '>=', $sixMonthsAgo)
            ->groupBy('month')
            ->orderBy('created_at')
            ->get()
            ->pluck('total', 'month')
            ->toArray();
    
        // Combinar los meses generados con las ventas reales
        return array_merge($months, $sales);
    }

    private function getTotalRevenue()
    {
        return DB::table('sales')->sum('total');
    }

    public function generateMonthlySalesReport()
    {
        $monthlySales = $this->getMonthlySales();
        $totalRevenue = $this->getTotalRevenue();

        $pdf = PDF::loadView('reports.monthly_sales', [
            'monthlySales' => $monthlySales,
            'totalRevenue' => $totalRevenue
        ]);

        return $pdf->download('monthly_sales_report.pdf');
    }
}
