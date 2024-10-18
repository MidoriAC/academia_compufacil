<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RoleController extends Controller
{
    public function index()
    {

        $roles = DB::table('roles')
    ->select('roles.*', DB::raw('GROUP_CONCAT(permissions.name) as permissions'))
    ->leftJoin('role_permission', 'roles.id', '=', 'role_permission.role_id')
    ->leftJoin('permissions', 'role_permission.permission_id', '=', 'permissions.id')
    ->groupBy('roles.id', 'roles.name', 'roles.descripcion', 'roles.created_at', 'roles.updated_at')
    ->get();

        return response()->json($roles);
    }

    // public function store(Request $request)
    // {
    //     // Crear un nuevo rol usando consulta directa
    //     $roleId = DB::table('roles')->insertGetId([
    //         'name' => $request->input('name'),
    //         'descripcion' => $request->input('description'),
    //         'created_at' => now(),
    //         'updated_at' => now(),
    //     ]);

    //     // Sincronizar permisos (eliminar y luego insertar los nuevos)
    //     DB::table('role_permission')->where('role_id', $roleId)->delete();

    //     foreach ($request->input('permissions', []) as $permissionId) {
    //         DB::table('role_permission')->insert([
    //             'role_id' => $roleId,
    //             'permission_id' => $permissionId,
    //         ]);
    //     }

    //         $role = DB::table('roles')
    //         ->select('roles.*', DB::raw('GROUP_CONCAT(permissions.name) as permissions'))
    //         ->leftJoin('role_permission', 'roles.id', '=', 'role_permission.role_id')
    //         ->leftJoin('permissions', 'role_permission.permission_id', '=', 'permissions.id')
    //         ->where('roles.id', $roleId)
    //         ->groupBy('roles.id', 'roles.name', 'roles.descripcion', 'roles.created_at', 'roles.updated_at')
    //         ->get();

    //     return response()->json($role);
    // }

    // public function update(Request $request, $roleId)
    // {
    //     // Actualizar el rol usando consulta directa
    //     DB::table('roles')
    //         ->where('id', $roleId)
    //         ->update([
    //             'name' => $request->input('name'),
    //             'descripcion' => $request->input('description'),
    //             'updated_at' => now(),
    //         ]);

    //     // Sincronizar permisos (eliminar y luego insertar los nuevos)
    //     DB::table('role_permission')->where('role_id', $roleId)->delete();

    //     foreach ($request->input('permissions', []) as $permissionId) {
    //         DB::table('role_permission')->insert([
    //             'role_id' => $roleId,
    //             'permission_id' => $permissionId,
    //         ]);
    //     }

    //     $role = DB::table('roles')
    // ->select('roles.*', DB::raw('GROUP_CONCAT(permissions.name) as permissions'))
    // ->leftJoin('role_permission', 'roles.id', '=', 'role_permission.role_id')
    // ->leftJoin('permissions', 'role_permission.permission_id', '=', 'permissions.id')
    // ->where('roles.id', $roleId)
    // ->groupBy('roles.id', 'roles.name', 'roles.descripcion', 'roles.created_at', 'roles.updated_at')
    // ->get();

    //     return response()->json($role);
    // }

//     public function store(Request $request)
// {
//     \Log::info('Received role data: ' . json_encode($request->all()));  // Para depuración

//     $roleId = DB::table('roles')->insertGetId([
//         'name' => $request->input('name'),
//         'descripcion' => $request->input('description'),
//         'created_at' => now(),
//         'updated_at' => now(),
//     ]);

//     // Sincronizar permisos
//     if ($request->has('permissions')) {
//         $permissions = $request->input('permissions');
//         foreach ($permissions as $permissionId) {
//             DB::table('role_permission')->insert([
//                 'role_id' => $roleId,
//                 'permission_id' => $permissionId,
//             ]);
//         }
//     }

//     $role = DB::table('roles')
//         ->select('roles.*', DB::raw('GROUP_CONCAT(permissions.name) as permissions'))
//         ->leftJoin('role_permission', 'roles.id', '=', 'role_permission.role_id')
//         ->leftJoin('permissions', 'role_permission.permission_id', '=', 'permissions.id')
//         ->where('roles.id', $roleId)
//         ->groupBy('roles.id', 'roles.name', 'roles.descripcion', 'roles.created_at', 'roles.updated_at')
//         ->first();

//     \Log::info('Role created: ' . json_encode($role));  // Para depuración

//     return response()->json($role);
// }

// public function update(Request $request, $roleId)
// {
//     \Log::info('Received role update data: ' . json_encode($request->all()));  // Para depuración

//     DB::table('roles')
//         ->where('id', $roleId)
//         ->update([
//             'name' => $request->input('name'),
//             'descripcion' => $request->input('description'),
//             'updated_at' => now(),
//         ]);

//     // Sincronizar permisos
//     DB::table('role_permission')->where('role_id', $roleId)->delete();
//     if ($request->has('permissions')) {
//         $permissions = $request->input('permissions');
//         foreach ($permissions as $permissionId) {
//             DB::table('role_permission')->insert([
//                 'role_id' => $roleId,
//                 'permission_id' => $permissionId,
//             ]);
//         }
//     }

//     $role = DB::table('roles')
//         ->select('roles.*', DB::raw(' GROUP_CONCAT(permissions.name) as permissions'))
//         ->leftJoin('role_permission', 'roles.id', '=', 'role_permission.role_id')
//         ->leftJoin('permissions', 'role_permission.permission_id', '=', 'permissions.id')
//         ->where('roles.id', $roleId)
//         ->groupBy('roles.id', 'roles.name', 'roles.descripcion', 'roles.created_at', 'roles.updated_at')
//         ->first();

//     \Log::info('Role updated: ' . json_encode($role));  // Para depuración

//     return response()->json($role);
// }
public function store(Request $request)
{
    // Validar la entrada
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'permissions' => 'required|array', // Asegúrate de que se reciban los permisos como un array
    ]);

    // Crear un nuevo rol
    $roleId = DB::table('roles')->insertGetId([
        'name' => $request->input('name'),
        'descripcion' => $request->input('description'),
        'created_at' => now(),
        'updated_at' => now(),
    ]);

    // Sincronizar permisos
    if ($request->has('permissions')) {
        foreach ($request->input('permissions') as $permissionId) {
            DB::table('role_permission')->insert([
                'role_id' => $roleId,
                'permission_id' => $permissionId,
            ]);
        }
    }

    // Obtener el rol creado
    $role = DB::table('roles')
        ->select('roles.*', DB::raw('GROUP_CONCAT(permissions.name) as permissions'))
        ->leftJoin('role_permission', 'roles.id', '=', 'role_permission.role_id')
        ->leftJoin('permissions', 'role_permission.permission_id', '=', 'permissions.id')
        ->where('roles.id', $roleId)
        ->groupBy('roles.id', 'roles.name', 'roles.descripcion', 'roles.created_at', 'roles.updated_at')
        ->first();

    return response()->json($role);
}
public function update(Request $request, $roleId)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'permissions' => 'required|array',
    ]);

    DB::table('roles')->where('id', $roleId)->update([
        'name' => $request->input('name'),
        'descripcion' => $request->input('description'),
        'updated_at' => now(),
    ]);

    // Sincronizar permisos
    DB::table('role_permission')->where('role_id', $roleId)->delete();
    foreach ($request->input('permissions') as $permissionId) {
        DB::table('role_permission')->insert([
            'role_id' => $roleId,
            'permission_id' => $permissionId,
        ]);
    }

    $role = DB::table('roles')
        ->select('roles.*', DB::raw('GROUP_CONCAT(permissions.name) as permissions'))
        ->leftJoin('role_permission', 'roles.id', '=', 'role_permission.role_id')
        ->leftJoin('permissions', 'role_permission.permission_id', '=', 'permissions.id')
        ->where('roles.id', $roleId)
        ->groupBy('roles.id', 'roles.name', 'roles.descripcion', 'roles.created_at', 'roles.updated_at')
        ->first();

    return response()->json($role);
}
public function update22(Request $request, $roleId)
{
    // Validar la entrada
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'permissions' => 'required|array', // Asegúrate de que se reciban los permisos como un array
    ]);

    // Actualizar el rol
    DB::table('roles')->where('id', $roleId)->update([
        'name' => $request->input('name'),
        'descripcion' => $request->input('description'),
        'updated_at' => now(),
    ]);

    // Sincronizar permisos
    DB::table('role_permission')->where('role_id', $roleId)->delete(); // Eliminar permisos existentes
    foreach ($request->input('permissions') as $permissionId) {
        DB::table('role_permission')->insert([
            'role_id' => $roleId,
            'permission_id' => $permissionId,
        ]);
    }

    // Obtener el rol actualizado
    $role = DB::table('roles')
        ->select('roles.*', DB::raw('GROUP_CONCAT(permissions.name) as permissions'))
        ->leftJoin('role_permission', 'roles.id', '=', 'role_permission.role_id')
        ->leftJoin('permissions', 'role_permission.permission_id', '=', ' permissions.id')
        ->where('roles.id', $roleId)
        ->groupBy('roles.id', 'roles.name', 'roles.descripcion', 'roles.created_at', 'roles.updated_at')
        ->first();

    return response()->json($role);
}
    public function destroy($roleId)
    {
        // Eliminar los permisos asociados al rol
        DB::table('role_permission')->where('role_id', $roleId)->delete();

        // Eliminar el rol
        DB::table('roles')->where('id', $roleId)->delete();

        return response()->json(['message' => 'Role deleted successfully']);
    }
}
