<?php

namespace App\Http\Controllers;
use App\Models\multimedia;
use Error;
use Exception;
use Illuminate\Http\Request;

class multimedia_controller extends Controller
{
    public function deleteMultimedia($id) {
        try {
            $multimedia = multimedia::findOrFail($id);
            $multimedia->delete();
            return response()->json([
                'message' => 'Multimedia deleted successfully'
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error deleting multimedia',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    public function getMultimedia($id) {
        try {
            $multimedia = multimedia::findOrFail($id);
            return response()->json([
                'multimedia' => $multimedia
            ], 200);
        } catch (Exception $e) {
            return response()->json([
                'message' => 'Error fetching multimedia',
                'error' => $e->getMessage()
            ], 404);
        }
    }
    public function updateMultimedia(Request $request) {
        $validacion = $request->validate(['url' => 'required|string|max:255']);
        try { 
            multimedia::where('id', $request->id)->update([
                'url' => $request->url,
            ]);
            multimedia::save();
            return response()->json([
                'message' => 'Multimedia updated successfully'
            ], 200);
        }
        catch(Error $err) {
            return response()->json(['message' => "No se encontro "])->status(200);
        }
    }
    public function createMultimedia(Request $request) {
        
        try{ $validate = $request->validate( [
            'id_empresa' => 'required|integer',
            'id_estudiante' => 'required|integer',
            'url' => 'required|string|max:255',
        ]); }
        catch(Error $err) { 
            return response()->json([
                'response' => 'error',
                'message' => 'Error in validation',
                'error' => $err->getMessage()
            ])->status(404);
        }
        $multimedia = multimedia::create([
            'id_empresa' => $request->id_empresa,
            'id_estudiante' => $request->id_estudiante,
            'url' => $request->url,

        ]);
        return response()->json([
            'message' => 'Multimedia created successfully',
            'multimedia' => $multimedia
        ], 201);
    } 
}