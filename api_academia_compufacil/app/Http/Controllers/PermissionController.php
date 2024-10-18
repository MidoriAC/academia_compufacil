<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PermissionController extends Controller
{
    //
    public function index()
    {
        // Obtener todos los permisos usando consulta directa
        $permissions = DB::table('permissions')->get();
        return response()->json($permissions);
    }

    public function store(Request $request)
    {
        // Insertar un nuevo permiso
        $permissionId = DB::table('permissions')->insertGetId([
            'name' => $request->input('name'),
            'description' => $request->input('description'),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        // Devolver el permiso recién creado
        $permission = DB::table('permissions')->where('id', $permissionId)->first();
        return response()->json($permission);
    }

    public function update(Request $request, $permissionId)
    {
        // Actualizar el permiso existente
        DB::table('permissions')
            ->where('id', $permissionId)
            ->update([
                'name' => $request->input('name'),
                'description' => $request->input('description'),
                'updated_at' => now(),
            ]);

        // Devolver el permiso actualizado
        $permission = DB::table('permissions')->where('id', $permissionId)->first();
        return response()->json($permission);
    }

    public function destroy($permissionId)
    {
        // Eliminar el permiso
        DB::table('permissions')->where('id', $permissionId)->delete();

        return response()->json(['message' => 'Permission deleted successfully']);
    }
}
