<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Resources\Course\CourseGCollection;

class CourseActivationController extends Controller
{
    //
    public function activateCurse(Request $request)
    {
        // Lógica para activar cursos
        $search = $request->search;
        $state = $request->state;

        $activaCurses = DB::table('sales')
            ->join('user', 'user.id', '=', 'sales.user_id')
            ->join('course_students', 'course_students.user_id', '=', 'user.id')
            ->where('course_students', 0)
            ->get();

        return response()->json([
            "activaCurses" => CourseGCollection::make($activaCurses),
        ]);
    }
}
