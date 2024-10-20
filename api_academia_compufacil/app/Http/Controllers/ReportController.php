<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

use Maatwebsite\Excel\Facades\Excel;
use App\Exports\CourseStatisticsExport;
use App\Exports\StudentStatisticsExport;

use PDF;

class ReportController extends Controller
{
    //
    public function getCourseStatistics(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');
        $categoryId = $request->input('category_id');

        $query = "SELECT c.id, c.title, COUNT(cs.id) as total_students, AVG(r.rating) as average_rating
                  FROM courses c
                  LEFT JOIN courses_students cs ON c.id = cs.course_id
                  LEFT JOIN reviews r ON c.id = r.course_id
                  WHERE 1=1";

        if ($startDate && $endDate) {
            $query .= " AND cs.created_at BETWEEN '$startDate' AND '$endDate'";
        }

        if ($categoryId) {
            $query .= " AND c.categorie_id = $categoryId";
        }

        $query .= " GROUP BY c.id, c.title";

        $statistics = DB::select($query);

        return response()->json(['statistics' => $statistics]);
    }

    public function getStudentStatistics(Request $request)
    {
        $startDate = $request->input('start_date');
        $endDate = $request->input('end_date');

        $query = "SELECT u.id, u.name, u.email, COUNT(cs.id) as total_courses, AVG(cs.progress) as average_progress
                  FROM users u
                  LEFT JOIN courses_students cs ON u.id = cs.user_id
                   WHERE u.role_id IS NULL";

        if ($startDate && $endDate) {
            $query .= " AND cs.created_at BETWEEN '$startDate' AND '$endDate'";
        }

        $query .= " GROUP BY u.id, u.name, u.email";

        $statistics = DB::select($query);

        return response()->json(['statistics' => $statistics]);
    }

    public function downloadCourseStatistics(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $statistics = $this->getCourseStatistics($request)->original['statistics'];

        if ($format === 'pdf') {
            $pdf = PDF::loadView('reports.course_statistics', ['statistics' => $statistics]);
            return $pdf->download('course_statistics.pdf');
        } elseif ($format === 'excel') {
            return Excel::download(new CourseStatisticsExport($statistics), 'course_statistics.xlsx');
        }
    }

    public function downloadStudentStatistics(Request $request)
    {
        $format = $request->input('format', 'pdf');
        $statistics = $this->getStudentStatistics($request)->original['statistics'];

        if ($format === 'pdf') {
            $pdf = PDF::loadView('reports.student_statistics', ['statistics' => $statistics]);
            return $pdf->download('student_statistics.pdf');
        } elseif ($format === 'excel') {
            return Excel::download(new StudentStatisticsExport($statistics), 'student_statistics.xlsx');
        }
    }
}
