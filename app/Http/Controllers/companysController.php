<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

class companysController extends Controller
{
    public function getAlllComapanys(){
        $companny = Empresa::all();
        return response()->json([
            'companys' => $companny
        ], 200);
    }
    public function createCompany(Request $request){
        $validate = Validator::make($request->all(),[
            'Nombre' => 'required|string|max:100',
            'Sector' => 'nullable|string|max:100',
            'Correo' => 'required|email|max:100',
            'Direccion' => 'nullable|string|max:255',
            'Contacto' => 'nullable|string|max:100',
            'Direccion_Web' => 'nullable|string|max:255',
        ]);
        if($validate->fails()){
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }
        $existingCompany = Empresa::where('Nombre', $request->input('Nombre'))->first();
        if ($existingCompany) {
            return response()->json([
                'message' => 'Company already exists'
            ], 409);
        }
        $companny = Empresa::create([
            'Nombre' => $request->Nombre,
            'Sector' => $request->Sector,
            'Correo' => $request->Correo,
            'Direccion' => $request->Direccion,
            'Contacto' => $request->Contacto,
            'Direccion_Web' => $request->Direccion_Web,
        ]);
        return response()->json([
            'company' => $companny
        ], 201);
    }
    public function getCompany($id){
        $companny = Empresa::find($id);
        if(!$companny){
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }
        return response()->json([
            'company' => $companny
        ], 200);
    }
    public function updateCompany(Request $request, $id){
        $companny = Empresa::find($id);
        $validate = Validator::make($request->all(),[
            'Nombre' => 'required|string|max:100',
            'Sector' => 'nullable|string|max:100',
            'Correo' => 'required|email|max:100',
            'Direccion' => 'nullable|string|max:255',
            'Contacto' => 'nullable|string|max:100',
            'Direccion_Web' => 'nullable|string|max:255',
        ]);
        if($validate->fails()){
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }
        if(!$companny){
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }
        $companny->update($request->all());
        return response()->json([
            'message' => 'Company updated successfully',
            'company' => $companny
        ], 200);
    }
    public function deleteCompany($id){
        $companny = Empresa::find($id);
        if(!$companny){
            return response()->json([
                'message' => 'Company not found'
            ], 404);
        }
        $companny->delete();
        return response()->json([
            'message' => 'Company deleted successfully'
        ], 200);
    }
}
