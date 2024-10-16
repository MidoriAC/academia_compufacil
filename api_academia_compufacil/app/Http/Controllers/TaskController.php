<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaskController extends Controller
{
    public function getCourses()
    {
        $courses = DB::table('courses')
            ->select('courses.id', 'courses.title', 'users.name as tutor_name', 
                DB::raw('(SELECT COUNT(*) FROM course_sections WHERE course_sections.course_id = courses.id) as class_count'),
                DB::raw('(SELECT COUNT(*) FROM courses_students WHERE courses_students.course_id = courses.id) as student_count'))
            ->join('users', 'users.id', '=', 'courses.user_id')
            ->where('courses.state', 2)
            ->get();

        return response()->json($courses);
    }

    public function getCourseClasses($courseId)
    {
        $classes = DB::table('course_clases')
            ->select('course_clases.id', 'course_clases.name', 
                DB::raw('(SELECT COUNT(*) FROM student_activities WHERE student_activities.class_id = course_clases.id) as task_count'))
            ->join('course_sections', 'course_sections.id', '=', 'course_clases.course_section_id')
            ->where('course_sections.course_id', $courseId)
            ->get();

        return response()->json($classes);
    }

    // public function getStudentTask($taskId)
    // {
    //     $task = DB::table('student_activities')
    //         ->select('student_activities.*', 'users.name as student_name')
    //         ->join('users', 'users.id', '=', 'student_activities.user_id')
    //         ->where('student_activities.id', $taskId)
    //         ->first();

    //     return response()->json($task);
    // }
    public function getStudentTask($taskId)
{
    $task = DB::table('student_activities')
        ->select('student_activities.*', 'users.name as student_name')
        ->join('users', 'users.id', '=', 'student_activities.user_id')
        ->where('student_activities.id', $taskId)
        ->first();

    // Si existe un campo que representa una imagen en student_activities, por ejemplo, 'imagen'
    if ($task && isset($task->file)) {
        $task->file = env("APP_URL") . "storage/" . $task->file;
    }

    return response()->json($task);
}
}