<?php

namespace App\Http\Controllers;

use App\Models\Usuario;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

class usersController extends Controller
{
    public function allUsers(){
        $user= Usuario::all();
        return response()->json([
            'users' => $user
        ], 200);
    }
    public function getUser($id){
        $user= Usuario::find($id);
        if(!$user){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        return response()->json([
            'user' => $user
        ], 200);
    }
    public function updateUser(Request $request, $id){
        $user= Usuario::find($id);
        $validato = Validator::make($request->all(), [
            'Usuario' => 'required|min:3|max:100',
            'Rol'=> 'required|in:admin,user',
            'Clave' => 'required|min:6|confirmed'
        ]);
        if ($validato->fails()) {
            return response()->json([
                'errors' => $validato->errors()
            ], 422);
        }
        if(!$user){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $user->update($request->all());
        return response()->json([
            'message' => 'User updated successfully',
            'user' => $user
        ], 200);
    }
    public function deleteUser($id){
        $user= Usuario::find($id);
        if(!$user){
            return response()->json([
                'message' => 'User not found'
            ], 404);
        }
        $user->delete();
        return response()->json([
            'message' => 'User deleted successfully'
        ], 200);
    }
}
