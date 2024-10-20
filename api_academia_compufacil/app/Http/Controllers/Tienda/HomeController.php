<?php

namespace App\Http\Controllers\Tienda;

use Carbon\Carbon;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Course\Course;
use App\Models\CoursesStudent;
use App\Models\Course\Categorie;
use App\Models\Discount\Discount;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use App\Http\Resources\Ecommerce\Course\CourseHomeResource;
use App\Http\Resources\Ecommerce\Course\CourseHomeCollection;
use App\Http\Resources\Ecommerce\LandingCourse\LandingCourseResource;

class HomeController extends Controller
{
    public function home(Request $request)
    {
        $categories = Categorie::where("categorie_id",NULL)->withCount("courses")->orderBy("id","desc")->get();
        
        $courses = Course::where("state",2)->inRandomOrder()->limit(3)->get();
        
        $categories_courses = Categorie::where("categorie_id",NULL) ->withCount(['courses' => function ($query) {
            $query->where('state', 2); // Filtra cursos con state 2
        }])
                        ->having("courses_count",">",0)
                        ->orderBy("id","desc")->take(5)->get();
        $group_courses_categories = collect([]);

        foreach ($categories_courses as $key => $categories_course) {
            $group_courses_categories->push([
                "id" => $categories_course->id,
                "name" => $categories_course->name,
                "name_empty" => str_replace(" ","",$categories_course->name),
                "courses_count" => $categories_course->courses_count,
                "courses" => CourseHomeCollection::make($categories_course->courses),
            ]);
        }

        date_default_timezone_set("America/Guatemala");
        $DESCOUNT_BANNER = Discount::where("type_campaing",3)->where("state",1)
                            ->where("start_date","<=",today())
                            ->where("end_date",">=",today())
                            ->first();

        $DESCOUNT_BANNER_COURSES = collect([]);
        if($DESCOUNT_BANNER){
            foreach ($DESCOUNT_BANNER->courses as $key => $course_discount) {
                $DESCOUNT_BANNER_COURSES->push(CourseHomeResource::make($course_discount->course));
            }
        }

        date_default_timezone_set("America/Guatemala");
        $DESCOUNT_FLASH = Discount::where("type_campaing",2)->where("state",1)
                            ->where("start_date","<=",today())
                            ->where("end_date",">=",today())
                            ->first();

        $DESCOUNT_FLASH_COURSES = collect([]);
        if($DESCOUNT_FLASH){
            $DESCOUNT_FLASH->end_date = Carbon::parse($DESCOUNT_FLASH->end_date)->addDays(1);
            foreach ($DESCOUNT_FLASH->courses as $key => $course_discount) {
                $DESCOUNT_FLASH_COURSES->push(CourseHomeResource::make($course_discount->course));
            }
        }

        return response()->json([
            "categories" => $categories->map(function($categorie){
                return [
                    "id" => $categorie->id,
                    "name" => $categorie->name,
                    "imagen" => env("APP_URL")."storage/".$categorie->imagen,
                    "course_count" => $categorie->courses_count,
                ];
            }),
            "courses_home" => CourseHomeCollection::make($courses),
            "group_courses_categories" => $group_courses_categories,
            "DESCOUNT_BANNER" => $DESCOUNT_BANNER,
            "DESCOUNT_BANNER_COURSES" => $DESCOUNT_BANNER_COURSES,

            "DESCOUNT_FLASH" => $DESCOUNT_FLASH ? [
                "id" => $DESCOUNT_FLASH->id,
                "discount" => $DESCOUNT_FLASH->discount,
                "code" => $DESCOUNT_FLASH->code,
                "type_campaing" => $DESCOUNT_FLASH->type_campaing,
                "type_discount" => $DESCOUNT_FLASH->type_discount,
                "end_date" => Carbon::parse($DESCOUNT_FLASH->end_date)->format("Y-m-d"), 
                "start_date_d" =>  Carbon::parse($DESCOUNT_FLASH->start_date)->format("Y/m/d"), 
                "end_date_d" =>  Carbon::parse($DESCOUNT_FLASH->end_date)->subDays(1)->format("Y/m/d"), 
            ] : NULL,
            "DESCOUNT_FLASH_COURSES" => $DESCOUNT_FLASH_COURSES,
        ]);
    }

    public function course_detail(Request $request,$slug)
    {
        $campaing_discount = $request->get("campaing_discount");
        $discount = null;
        if($campaing_discount){
            $discount = Discount::findOrFail($campaing_discount);
        }
        $course = Course::where("slug",$slug)->first();
        $is_have_course = false;
        if(!$course){
            return abort(404);
        }
        if(Auth::guard("api")->check()){
            $course_student = CoursesStudent::where("user_id",auth("api")->user()->id)->where("course_id",$course->id)->first();
            if($course_student){
                $is_have_course = true;
            }
        }
        $courses_related_instructor = Course::where("id","<>",$course->id)->where("user_id",$course->user_id)->where('state', 2)->inRandomOrder()->take(2)->get();

        $courses_related_categories = Course::where("id","<>",$course->id)->where("categorie_id",$course->categorie_id)->where('state', 2)->inRandomOrder()->take(3)->get();

        return response()->json([
            "course" => LandingCourseResource::make($course),
            "courses_related_instructor" => $courses_related_instructor->map(function($course){
                return CourseHomeResource::make($course);
            }),
            "courses_related_categories" => $courses_related_categories->map(function($course){
                return CourseHomeResource::make($course);
            }),
            "DISCOUNT" => $discount,
            "is_have_course" => $is_have_course,
        ]);
    }

    public function course_leason(Request $request,$slug)
    {

        $course = Course::where("slug",$slug)->first();

        if(!$course){
            return response()->json(["message" => 403, "message_text" => "EL CURSO NO EXISTE"]);
        }

        $course_student = CoursesStudent::where("course_id",$course->id)->where("user_id",auth("api")->user()->id)->first();
        if(!$course_student){
            return response()->json(["message" => 403, "message_text" => "TU NO ESTAS INSCRITO EN ESTE CURSO"]);
        }
        
        return response()->json([
            "course" => LandingCourseResource::make($course),
        ]);
    }

    //     public function course_leason(Request $request, $slug)
    // {
    //     $course = Course::where("slug", $slug)->first();

    //     if (!$course) {
    //         return response()->json(["message" => 403, "message_text" => "EL CURSO NO EXISTE"]);
    //     }

    //     $course_student = CoursesStudent::where("course_id", $course->id)
    //         ->where("user_id", auth("api")->user()->id)
    //         ->first();

    //     if (!$course_student) {
    //         return response()->json(["message" => 403, "message_text" => "TU NO ESTAS INSCRITO EN ESTE CURSO"]);
    //     }

    //     // Actualizar el progreso
    //     $total_classes = $course->course_clases()->count();
    //     $completed_classes = $course_student->clases_checkeds ? count(json_decode($course_student->clases_checkeds)) : 0;
    //     $progress = ($completed_classes / $total_classes) * 100;

    //     $course_student->update([
    //         'progress' => $progress
    //     ]);

    //     $course->load('course_clases');

    //     return response()->json([
    //         "course" => LandingCourseResource::make($course),
    //         "progress" => $progress,
    //         "completed_classes" => $completed_classes,
    //         "total_classes" => $total_classes,
    //     ]);
    // }
//     public function course_leason(Request $request, $slug)
// {
//     $course = DB::table('courses')->where('slug', $slug)->first();

//     if (!$course) {
//         return response()->json(["message" => 403, "message_text" => "EL CURSO NO EXISTE"]);
//     }

//     $course_student = DB::table('courses_students')
//         ->where('course_id', $course->id)
//         ->where('user_id', auth('api')->user()->id)
//         ->first();

//     if (!$course_student) {
//         return response()->json(["message" => 403, "message_text" => "TU NO ESTAS INSCRITO EN ESTE CURSO"]);
//     }

//     // Obtener el total de clases del curso
//     $total_classes = DB::table('course_clases')
//         ->join('course_sections', 'course_clases.course_section_id', '=', 'course_sections.id')
//         ->where('course_sections.course_id', $course->id)
//         ->count();

//     // Calcular las clases completadas y el progreso
//     $completed_classes = $course_student->clases_checkeds ? count(json_decode($course_student->clases_checkeds)) : 0;
//     $progress = $total_classes > 0 ? ($completed_classes / $total_classes) * 100 : 0;

//     // Actualizar el progreso del estudiante
//     DB::table('courses_students')
//         ->where('id', $course_student->id)
//         ->update(['progress' => $progress]);

//     // Obtener las clases del curso
//     $course_clases = DB::table('course_clases')
//         ->join('course_sections', 'course_clases.course_section_id', '=', 'course_sections.id')
//         ->where('course_sections.course_id', $course->id)
//         ->select('course_clases.*', 'course_sections.name as section_name')
//         ->get();

//     // Aquí deberías adaptar LandingCourseResource para trabajar con objetos estándar en lugar de modelos Eloquent
//     $course_data = [
//         'id' => $course->id,
//         'title' => $course->title,
//         'slug' => $course->slug,
//         // ... otros campos del curso que necesites
//         'course_clases' => $course_clases
//     ];

//     return response()->json([
//         "course" => $course_data, // Reemplaza LandingCourseResource::make($course) con $course_data
//         "progress" => $progress,
//         "completed_classes" => $completed_classes,
//         "total_classes" => $total_classes,
//     ]);
// }

// public function course_leason(Request $request, $slug)
// {
//     $course = DB::table('courses')->where('slug', $slug)->first();

//     if (!$course) {
//         return response()->json(["message" => 403, "message_text" => "EL CURSO NO EXISTE"]);
//     }

//     $course_student = DB::table('courses_students')
//         ->where('course_id', $course->id)
//         ->where('user_id', auth('api')->user()->id)
//         ->first();

//     if (!$course_student) {
//         return response()->json(["message" => 403, "message_text" => "TU NO ESTAS INSCRITO EN ESTE CURSO"]);
//     }

//     // Obtener las secciones del curso
//     $sections = DB::table('course_sections')
//         ->where('course_id', $course->id)
//         ->get();

//     $malla = [];
//     foreach ($sections as $section) {
//         $clases = DB::table('course_clases')
//             ->where('course_section_id', $section->id)
//             ->get();

//         $malla[] = [
//             'id' => $section->id,
//             'name' => $section->name,
//             'clases' => $clases
//         ];
//     }

//     // Calcular el progreso
//     $total_classes = DB::table('course_clases')
//         ->join('course_sections', 'course_clases.course_section_id', '=', 'course_sections.id')
//         ->where('course_sections.course_id', $course->id)
//         ->count();

//     $completed_classes = $course_student->clases_checkeds ? count(json_decode($course_student->clases_checkeds)) : 0;
//     $progress = $total_classes > 0 ? ($completed_classes / $total_classes) * 100 : 0;

//     // Actualizar el progreso del estudiante
//     DB::table('courses_students')
//         ->where('id', $course_student->id)
//         ->update(['progress' => $progress]);

//     // Obtener información del instructor
//     $instructor = DB::table('users')
//         ->where('id', $course->user_id)
//         ->first();

//     $course_data = [
//         'id' => $course->id,
//         'title' => $course->title,
//         'slug' => $course->slug,
//         'subtitle' => $course->subtitle,
//         'imagen' => $course->imagen,
//         'malla' => $malla,
//         'instructor' => [
//             'id' => $instructor->id,
//             'full_name' => $instructor->name . ' ' . $instructor->surname,
//             'avatar' => $instructor->avatar,
//             'profesion' => $instructor->profesion,
//             'description' => $instructor->description,
//         ],
//         'requirements' => json_decode($course->requirements),
//         'who_is_it_for' => json_decode($course->who_is_it_for),
//         'description' => $course->description,
//         // Agrega otros campos necesarios aquí
//     ];

//     return response()->json([
//         "course" => $course_data,
//         "progress" => $progress,
//         "completed_classes" => $completed_classes,
//         "total_classes" => $total_classes,
//     ]);
// }

    public function listCourses(Request $request)
    {
        $search = $request->search;
        $selected_categories = $request->selected_categories ?? [];
        $instructores_selected = $request->instructores_selected ?? [];
        $min_price = $request->min_price;
        $max_price = $request->max_price;

        $idiomas_selected = $request->idiomas_selected ?? [];
        $levels_selected = $request->levels_selected ?? [];
        $rating_selected = $request->rating_selected;

        $courses_a = [];
        if($rating_selected){
            $courses_query = Course::where("state",2)
                         ->join("reviews","reviews.course_id" ,"=", "courses.id")
                         ->select("courses.id as courseId",DB::raw("AVG(reviews.rating) as rating_reviews"))
                         ->groupBy("courseId")
                         ->having("rating_reviews",">=",$rating_selected) // 3.6
                         ->having("rating_reviews","<",$rating_selected + 1)
                         ->get();
            $courses_a= $courses_query->pluck("courseId")->toArray();
            // error_log(json_encode($courses_a));
        }
        // if(!$search){
        //     return response()->json(["courses" => []]);
        // }
        $courses = Course::filterAdvanceEcommerce($search,
                            $selected_categories,
                            $instructores_selected,
                            $min_price,$max_price,
                            $idiomas_selected,$levels_selected,
                            $courses_a,$rating_selected)->where("state",2)->orderBy("id","desc")->get();

        return response()->json(["courses" => CourseHomeCollection::make($courses)]);
    }

    public function config_all()
    {
        $categories = Categorie::where("categorie_id",NULL)->withCount("courses")->orderBy("id","desc")->get();
        $instructores = User::where("is_instructor",1)->orderBy("id","desc")->get();
        return response()->json([
            "categories" => $categories,
            "instructores" => $instructores->map(function($user){
                return [
                    "id" => $user->id,
                    "courses_count" => $user->courses_count,
                    "full_name" => $user->name.' '.$user->surname,
                ];
            }),
            "levels" => ["Basico","Intermedio","Avanzado"],
            "idiomas" => ["Español","Ingles","Portuges"],
        ]);
    }


    //*Subir actividad:
            public function uploadActivity(Request $request)
        {
            $request->validate([
                'course_id' => 'required|exists:courses,id',
                'class_id' => 'required|exists:course_clases,id',
                'file' => 'required|file|max:10240', // 10MB max
                'comments' => 'nullable|string',
            ]);

            $file = $request->file('file');
            $fileName = time() . '_' . $file->getClientOriginalName();
            $filePath = $file->storeAs('activities', $fileName, 'public');

            $activity = DB::table('student_activities')->insert([
                'user_id' => auth('api')->id(),
                'course_id' => $request->course_id,
                'class_id' => $request->class_id,
                'file' => $filePath,
                'comments' => $request->comments,
                'submitted_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);

            return response()->json(['message' => 'Activity uploaded successfully', 'file_path' => $filePath]);
        }
}
