<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class SalesController extends Controller
{
    //
    public function index(Request $request)
    {
        $search = $request->search;
        $query = DB::table('sales')
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->select('sales.*', 'users.name as user_name');

        if ($search) {
            $query->where('users.name', 'like', "%{$search}%");
        }

        $sales = $query->get();

        $sales = $sales->map(function ($sale) {
            $sale->n_transaccion = env("APP_URL") . "storage/" . $sale->n_transaccion;
            return $sale;
        });

        return response()->json(['sales' => $sales]);
    }
    public function show($id)
{
    // Obtener la venta y el user_id
    $sale = DB::table('sales')
        ->where('sales.id', $id)
        ->join('users', 'sales.user_id', '=', 'users.id')
        ->select('sales.*', 'users.name as user_name')
        ->first();

    if (!$sale) {
        return response()->json(['message' => 'Sale not found'], 404);
    }

    // Obtener los detalles de la venta
    $saleDetails = DB::table('sale_details')
        ->where('sale_details.sale_id', $id)
        ->join('courses', 'sale_details.course_id', '=', 'courses.id')
        ->leftJoin('courses_students', function ($join) use ($sale) {
            $join->on('courses_students.course_id', '=', 'courses.id')
                ->where('courses_students.user_id', $sale->user_id); // Cambiar `on` a `where`
        })
        ->select('sale_details.*', 'courses.title as course_title', 'courses_students.state as course_student_state', 'courses_students.id as course_student_id')
        ->get();

    $sale->details = $saleDetails;

    return response()->json(['sale' => $sale]);
}

    public function show2($id)
{
    // Obtener la venta y el user_id
    $sale = DB::table('sales')
        ->where('sales.id', $id)
        ->join('users', 'sales.user_id', '=', 'users.id')
        ->select('sales.*', 'users.name as user_name')
        ->first();

    if (!$sale) {
        return response()->json(['message' => 'Sale not found'], 404);
    }

    // Obtener los detalles de la venta
    $saleDetails = DB::table('sale_details')
        ->where('sale_details.sale_id', $id)
        ->join('courses', 'sale_details.course_id', '=', 'courses.id')
        ->leftJoin('courses_students', function ($join) use ($sale) { // Usar la venta obtenida
            $join->on('courses_students.course_id', '=', 'courses.id')
                ->on('courses_students.user_id', '=', $sale->user_id); // Ahora se puede usar el user_id
        })
        ->select('sale_details.*', 'courses.title as course_title', 'courses_students.state as course_student_state', 'courses_students.id as course_student_id')
        ->get();

    $sale->details = $saleDetails;

    return response()->json(['sale' => $sale]);
}
    public function show1($id)
    {
        $sale = DB::table('sales')
            ->where('sales.id', $id)
            ->join('users', 'sales.user_id', '=', 'users.id')
            ->select('sales.*', 'users.name as user_name')
            ->first();

        if (!$sale) {
            return response()->json(['message' => 'Sale not found'], 404);
        }

        $saleDetails = DB::table('sale_details')
            ->where('sale_details.sale_id', $id)
            ->join('courses', 'sale_details.course_id', '=', 'courses.id')
            ->leftJoin('courses_students', function ($join) {
                $join->on('courses_students.course_id', '=', 'courses.id')
                    ->on('courses_students.user_id', '=', 'sales.user_id');
            })
            ->select('sale_details.*', 'courses.title as course_title', 'courses_students.state as course_student_state', 'courses_students.id as course_student_id')
            ->get();

        $sale->details = $saleDetails;

        return response()->json(['sale' => $sale]);
    }

    public function updateCourseStudentState(Request $request, $id)
    {
        $affected = DB::table('courses_students')
            ->where('id', $id)
            ->update(['state' => $request->state]);

        if ($affected) {
            return response()->json(['message' => 'Course student state updated successfully']);
        } else {
            return response()->json(['message' => 'Course student not found'], 404);
        }
    }
}
