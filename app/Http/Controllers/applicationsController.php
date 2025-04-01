<?php

namespace App\Http\Controllers;

use App\Models\Postulacion;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Http\Request;

class applicationsController extends Controller
{
    public function allAplications(){
        $aplications = Postulacion::all();
        return response()->json([
            'aplications' => $aplications
        ], 200);
    }

    //las aplicacione seran solo para los postulantes, es decir estudiantes registrador
    //en la plataforma, por lo que se necesita que el id del candidato sea el que este logueado en ese momento
    public function createAplication(Request $request){
        $validate = Validator::make($request->all(), [
            'Id_Trabajo' => 'required|integer',
            'Id_Candidato' => 'required|integer',
            'Estado' => 'required|string|max:100',
            'Fecha_Envio' => 'required|date',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }
        $aplication = Postulacion::create([
            'Id_Trabajo' => $request->Id_Trabajo,
            'Id_Candidato' => $request->Id_Candidato,
            'Estado' => $request->Estado,
            'Fecha_Envio' => $request->Fecha_Envio,
        ]);
        return response()->json([
            'aplication' => $aplication
        ], 201);
    }
    public function getAplication($id){
        $aplication = Postulacion::find($id);
        if (!$aplication) {
            return response()->json([
                'message' => 'No se encontró la aplicación'
            ], 404);
        }
        return response()->json([
            'aplication' => $aplication
        ], 200);
    }
    public function updateAplication(Request $request, $id){
        $aplication = Postulacion::find($id);
        if (!$aplication) {
            return response()->json([
                'message' => 'No se encontró la aplicación'
            ], 404);
        }
        $validate = Validator::make($request->all(), [
            'Id_Trabajo' => 'required|integer',
            'Id_Candidato' => 'required|integer',
            'Estado' => 'required|string|max:100',
            'Fecha_Envio' => 'required|date',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }
        $aplication->update($request->all());
        return response()->json([
            'aplication' => $aplication
        ], 200);
    }
    public function deleteAplication($id){
        $aplication = Postulacion::find($id);
        if (!$aplication) {
            return response()->json([
                'message' => 'No se encontró la aplicación'
            ], 404);
        }
        $aplication->delete();
        return response()->json([
            'message' => 'Aplicación eliminada correctamente'
        ], 200);
    }
}
