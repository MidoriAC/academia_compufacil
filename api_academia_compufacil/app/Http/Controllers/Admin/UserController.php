<?php

namespace App\Http\Controllers\Admin;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use App\Http\Resources\User\UserGResource;
use App\Http\Resources\User\UserGCollection;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $search = $request->search;
        $state = $request->state;

        $users = User::filterAdvance($search,$state)->where("type_user",2)->orderby("id","desc")->get();

        return response()->json([
            "users" => UserGCollection::make($users),
        ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // if($request->hasFile("imagen")){
        //     $path = Storage::putFile("users",$request->file("imagen"));
        //     $request->request->add(["avatar" => $path]);
        // }
        if($request->hasFile("imagen")){
            // Guardar la imagen en el disco 'public'
            $path = $request->file("imagen")->store('users', 'public');
            $request->request->add(["avatar" => $path]);
        }
        
        if($request->password){
            $request->request->add(["password" => bcrypt($request->password)]);
        }
        $user = User::create($request->all());

        if ($request->has('role_id')) {
            $user->role_id = $request->role_id;
            $user->save();
        }

        return response()->json(["user" => UserGResource::make($user)]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        if($request->hasFile("imagen")){
            if($user->avatar){
                Storage::delete($user->avatar);
            }
            $path = Storage::putFile("users",$request->file("imagen"));
            $request->request->add(["avatar" => $path]);
        }
        if($request->password){
            $request->request->add(["password" => bcrypt($request->password)]);
        }
        $user->update($request->all());

        if ($request->has('role_id')) {
            $user->role_id = $request->role_id;
            $user->save();
        }

        return response()->json(["user" => UserGResource::make($user)]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();
        return response()->json(["message" => 200]);
    }

    //*Funciones para roles
        public function getRole($role_id)
    {
        return DB::table('roles')->where('id', $role_id)->first();
    }

        public function hasPermission($permission)
    {
        // Obtener el rol del usuario
        $role = DB::table('roles')->where('id', $this->role_id)->first();

        if ($role) {
            // Obtener los permisos asociados a ese rol
            $permissions = DB::table('permissions')
                ->join('role_permissions', 'permissions.id', '=', 'role_permissions.permission_id')
                ->where('role_permissions.role_id', $role->id)
                ->pluck('permissions.name');

            // Verificar si el permiso especificado está en la lista
            return $permissions->contains($permission);
        }

        return false;
    }

    public function getUserRole($userId)
    {
        $user = User::findOrFail($userId);
        $role = DB::table('roles')->where('id', $user->role_id)->first();
        return response()->json($role);
    }

    // public function getUserPermissions($userId)
    // {
    //     $user = User::findOrFail($userId);
    //     $permissions = DB::table('permissions')
    //         ->join('role_permissions', 'permissions.id', '=', 'role_permissions.permission_id')
    //         ->where('role_permissions.role_id', $user->role_id)
    //         // ->select('permissions.*')
    //         ->pluck('permissions.name') 
    //         ->toArray();
    //     return response()->json($permissions);
    // }
//     public function getUserPermissions($userId)
// {
//     $user = User::findOrFail($userId);
//     $permissions = DB::table('permissions')
//         ->join('role_permission', 'permissions.id', '=', 'role_permission.permission_id')
//         ->where('role_permission.role_id', $user->role_id)
//         ->pluck('permissions.name')
//         ->toArray();
    
//     return response()->json($permissions);
// }

public function getUserPermissions($userId)
{
    $user = User::findOrFail($userId);
    $permissions = DB::table('permissions')
        ->join('role_permission', 'permissions.id', '=', 'role_permission.permission_id')
        ->where('role_permission.role_id', $user->role_id)
        ->pluck('permissions.name')
        ->toArray();
    return response()->json($permissions);
}
}
