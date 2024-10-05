<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
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
}
