<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CertificateController extends Controller
{
    //
    public function index()
    {
        $students = DB::table('users')
            ->join('courses_students', 'users.id', '=', 'courses_students.user_id')
            ->join('courses', 'courses_students.course_id', '=', 'courses.id')
            ->leftJoin('certificates', function ($join) {
                $join->on('users.id', '=', 'certificates.user_id')
                    ->on('courses.id', '=', 'certificates.course_id');
            })
            ->select('users.id', 'users.name', 'users.email', 
                     'courses.id as course_id', 'courses.title as course_title', 
                     'certificates.id as certificate_id', 'certificates.file_path')
            ->get()
            ->groupBy('id')
            ->map(function ($student) {
                $firstStudent = $student->first();
                return [
                    'id' => $firstStudent->id,
                    'name' => $firstStudent->name,
                    'email' => $firstStudent->email,
                    'courses' => $student->map(function ($course) {
                        return [
                            'course_id' => $course->course_id,
                            'course_title' => $course->course_title,
                            'certificate_id' => $course->certificate_id,
                            'file_path' => $course->file_path,
                        ];
                    }),
                ];
            })
            ->values();
    
        return response()->json(['students' => $students]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'course_id' => 'required|exists:courses,id',
            'certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $file = $request->file('certificate');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('certificates', $fileName, 'public');

        $certificate = DB::table('certificates')->insertGetId([
            'user_id' => $request->user_id,
            'course_id' => $request->course_id,
            'file_path' => $filePath,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return response()->json(['message' => 'Certificate uploaded successfully', 'certificate_id' => $certificate]);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'certificate' => 'required|file|mimes:pdf,jpg,jpeg,png|max:2048',
        ]);

        $certificate = DB::table('certificates')->where('id', $id)->first();

        if (!$certificate) {
            return response()->json(['message' => 'Certificate not found'], 404);
        }

        $file = $request->file('certificate');
        $fileName = time() . '_' . $file->getClientOriginalName();
        $filePath = $file->storeAs('certificates', $fileName, 'public');

        DB::table('certificates')
            ->where('id', $id)
            ->update([
                'file_path' => $filePath,
                'updated_at' => now(),
            ]);

        return response()->json(['message' => 'Certificate updated successfully']);
    }


    public function download($userId, $courseId)
{
    try {
        $certificate = DB::table('certificates')
            ->where('user_id', $userId)
            ->where('course_id', $courseId)
            ->first();

        if (!$certificate) {
            return response()->json(['message' => 'Certificado no encontrado'], 404);
        }

        $filePath = storage_path('app/public/' . $certificate->file_path);
        if (!file_exists($filePath)) {
            return response()->json(['message' => 'Archivo del certificado no encontrado'], 404);
        }

        return response()->download($filePath);
    } catch (\Exception $e) {
        return response()->json(['message' => 'Error interno del servidor: ' . $e->getMessage()], 500);
    }
}
}
