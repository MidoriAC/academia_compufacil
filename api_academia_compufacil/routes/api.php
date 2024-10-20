<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Tienda\CartController;
use App\Http\Controllers\Tienda\HomeController;
use App\Http\Controllers\Tienda\ReviewController;
use App\Http\Controllers\Tienda\CheckoutController;
use App\Http\Controllers\Admin\Coupon\CouponController;
use App\Http\Controllers\Admin\Course\ClaseGController;
use App\Http\Controllers\Admin\Course\CourseGController;
use App\Http\Controllers\Tienda\ProfileClientController;
use App\Http\Controllers\Admin\Course\SeccionGController;
use App\Http\Controllers\Admin\Course\CategorieController;
use App\Http\Controllers\Admin\Discount\DiscountController;
use App\Http\Controllers\CheckoutStoreController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\TaskController;
use App\Http\Controllers\RoleController;
use App\Http\Controllers\PermissionController;
use App\Http\Controllers\CertificateController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

// Route::middleware('auth:api')->get('/user', function (Request $request) {
//     return $request->user();
// });
// Route::post('/checkout-with-comprobante', [CheckoutController::class, 'checkoutWithComprobante'])->middleware('auth:api');
Route::group([
    'middleware' => 'api',
    'prefix' => 'auth'
], function ($router) {
    Route::post('/register', [AuthController::class, 'register'])->name('register');
    Route::post('/login', [AuthController::class, 'login'])->name('login');
    Route::post('/login_tienda', [AuthController::class, 'login_tienda']);
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::post('/refresh', [AuthController::class, 'refresh'])->name('refresh');
    Route::post('/me', [AuthController::class, 'me'])->name('me');
    Route::post('/checkout-with-comprobante', [CheckoutController::class, 'checkoutWithComprobante']);

});

Route::group([
    'middleware' => 'api',
], function ($router) {
    Route::resource('/users',UserController::class);
    Route::post('/users/{id}',[UserController::class, "update"]);


    //*Roles:
      // Nuevas rutas para roles y permisos de usuarios
      Route::get('users/{id}/role', [UserController::class, 'getUserRole']);
      Route::get('users/{id}/permissions', [UserController::class, 'getUserPermissions']);
      Route::apiResource('roles', RoleController::class);
        Route::apiResource('permissions', PermissionController::class);
        Route::put('/roles/{id}', [RoleController::class, 'update']);

        //*Obtener roles dinamicamente
        Route::get('/roles', [UserController::class, 'getRoles']);

    //*Certificados
    Route::get('/students-certificates', [CertificateController::class, 'index']);
    Route::post('/certificates', [CertificateController::class, 'store']);
    Route::post('/certificates/{id}', [CertificateController::class, 'update']);




    // 
    Route::resource('/categorie',CategorieController::class);
    Route::post('/categorie/{id}',[CategorieController::class, "update"]);

    // 
    Route::get('/course/config',[CourseGController::class, "config"]);
    Route::resource('/course',CourseGController::class);
    Route::post('/course/upload_video/{id}',[CourseGController::class, "upload_video"]);
    Route::post('/course/{id}',[CourseGController::class, "update"]);
    
    Route::resource('/course-section',SeccionGController::class);
    
    Route::resource('/course-clases',ClaseGController::class);
    Route::post('/course-clases-file',[ClaseGController::class, "addFiles"]);
    Route::delete('/course-clases-file/{id}',[ClaseGController::class, "removeFiles"]);
    Route::post('/course-clases/upload_video/{id}',[ClaseGController::class, "upload_video"]);
    
    Route::get('/coupon/config',[CouponController::class, "config"]);
    Route::resource('/coupon',CouponController::class);
    
    Route::resource('/discount',DiscountController::class);

    Route::post('/course/activate', [CourseActivationController::class, 'activateCurse']);

    //Rutas para el detalle de la venta
    Route::get('/sales', [SalesController::class, 'index']);
    Route::get('/sales/{id}', [SalesController::class, 'show']);
    Route::put('/course-students/{id}', [SalesController::class, 'updateCourseStudentState']);

    //Ruta para el dashboard
    Route::get('/dashboard-stats', [DashboardController::class, 'getDashboardStats']);

    //*Rutas para ver las tareas/actividades del estudiante

    Route::get('/tasks/courses', [TaskController::class, 'getCourses']);
    Route::get('/tasks/courses/{courseId}/classes', [TaskController::class, 'getCourseClasses']);
    Route::get('/tasks/{taskId}', [TaskController::class, 'getStudentTask']);

     //*Rutas para reportería
     Route::get('/reports/course-statistics', [ReportController::class, 'getCourseStatistics']);
     Route::get('/reports/student-statistics', [ReportController::class, 'getStudentStatistics']);
    //  Route::get('/reports/course-statistics/download', [ReportController::class, 'downloadCourseStatistics']);
    //  Route::get('/reports/student-statistics/download', [ReportController::class, 'downloadStudentStatistics']);
    Route::match(['GET', 'OPTIONS'], '/reports/course-statistics/download', [ReportController::class, 'downloadCourseStatistics']);
    Route::match(['GET', 'OPTIONS'], '/reports/student-statistics/download', [ReportController::class, 'downloadStudentStatistics']);

    Route::get('/monthly-sales-report', [DashboardController::class, 'generateMonthlySalesReport']);
});


//*Rutas para el lado de la app del estudiante
Route::group(["prefix" => "ecommerce"],function($router){
    Route::get("home",[HomeController::class,"home"]);
    Route::get("config_all",[HomeController::class,"config_all"]);
    Route::post("list_courses",[HomeController::class,"listCourses"]);

    Route::get("course-detail/{slug}",[HomeController::class,"course_detail"]);
    
    Route::group([
        'middleware' => 'api',
    ], function ($router) {
        Route::get("course_leason/{slug}",[HomeController::class,"course_leason"]);
        Route::post('/apply_coupon',[CartController::class, "apply_coupon"]);
        Route::resource('/cart',CartController::class);
        Route::post('/checkout',[CheckoutController::class,"store"]);
        Route::post('/profile',[ProfileClientController::class,"profile"]);
        Route::post('/update_client',[ProfileClientController::class,"update_client"]);
        Route::resource('/review',ReviewController::class);

        //Procese de checkout del estudiante
        Route::post('/checkout-with-comprobante', [CheckoutStoreController::class, 'checkoutWithComprobante']);

        //* Subir tareas
        Route::post('/upload_activity', [HomeController::class, 'uploadActivity']);

        //* Descargar certificado
        // Route::get('/download_certificate/{courseId}', [CertificateController::class, 'download']);
        Route::get('/download_certificate/{userId}/{courseId}', [CertificateController::class, 'download']);
    });
});