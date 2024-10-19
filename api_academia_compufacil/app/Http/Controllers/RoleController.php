<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
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
//     // Validar la entrada
//     $request->validate([
//         'name' => 'required|string|max:255',
//         'description' => 'nullable|string',
//         'permissions' => 'required|array', // Asegúrate de que se reciban los permisos como un array
//     ]);

//     // Crear un nuevo rol
//     $roleId = DB::table('roles')->insertGetId([
//         'name' => $request->input('name'),
//         'descripcion' => $request->input('description'),
//         'created_at' => now(),
//         'updated_at' => now(),
//     ]);

//     // Sincronizar permisos
//     if ($request->has('permissions')) {
//         foreach ($request->input('permissions') as $permissionId) {
//             DB::table('role_permission')->insert([
//                 'role_id' => $roleId,
//                 'permission_id' => $permissionId,
//             ]);
//         }
//     }

//     // Obtener el rol creado
//     $role = DB::table('roles')
//         ->select('roles.*', DB::raw('GROUP_CONCAT(permissions.name) as permissions'))
//         ->leftJoin('role_permission', 'roles.id', '=', 'role_permission.role_id')
//         ->leftJoin('permissions', 'role_permission.permission_id', '=', 'permissions.id')
//         ->where('roles.id', $roleId)
//         ->groupBy('roles.id', 'roles.name', 'roles.descripcion', 'roles.created_at', 'roles.updated_at')
//         ->first();

//     return response()->json($role);
// }

public function store(Request $request)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'permissions' => 'required|array|min:1',
        'permissions.*' => 'exists:permissions,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    DB::beginTransaction();

    try {
        $roleId = DB::table('roles')->insertGetId([
            'name' => $request->input('name'),
            'descripcion' => $request->input('description'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        foreach ($request->input('permissions') as $permissionId) {
            DB::table('role_permission')->insert([
                'role_id' => $roleId,
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::commit();

        $role = $this->getRoleWithPermissions($roleId);

        return response()->json($role);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Error creating role: ' . $e->getMessage()], 500);
    }
}

public function update(Request $request, $roleId)
{
    $validator = Validator::make($request->all(), [
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'permissions' => 'required|array|min:1',
        'permissions.*' => 'exists:permissions,id',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    DB::beginTransaction();

    try {
        DB::table('roles')->where('id', $roleId)->update([
            'name' => $request->input('name'),
            'descripcion' => $request->input('description'),
            'updated_at' => now(),
        ]);

        DB::table('role_permission')->where('role_id', $roleId)->delete();

        foreach ($request->input('permissions') as $permissionId) {
            DB::table('role_permission')->insert([
                'role_id' => $roleId,
                'permission_id' => $permissionId,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::commit();

        $role = $this->getRoleWithPermissions($roleId);

        return response()->json($role);
    } catch (\Exception $e) {
        DB::rollBack();
        return response()->json(['error' => 'Error updating role: ' . $e->getMessage()], 500);
    }
}

private function getRoleWithPermissions($roleId)
{
    return DB::table('roles')
        ->select('roles.*', DB::raw('GROUP_CONCAT(permissions.id) as permissions'))
        ->leftJoin('role_permission', 'roles.id', '=', 'role_permission.role_id')
        ->leftJoin('permissions', 'role_permission.permission_id', '=', 'permissions.id')
        ->where('roles.id', $roleId)
        ->groupBy('roles.id', 'roles.name', 'roles.descripcion', 'roles.created_at', 'roles.updated_at')
        ->first();
}


}
