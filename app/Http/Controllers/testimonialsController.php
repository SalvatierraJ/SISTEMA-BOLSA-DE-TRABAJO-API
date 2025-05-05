<?php

namespace App\Http\Controllers;

use App\Models\Testimonio;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;

class testimonialsController extends Controller
{
    public function allTestimonials(){
        $testimonials = Testimonio::with(['usuario.multimedia'])
                        ->get();
        return response()->json([
            'testimonials' => $testimonials
        ], 200);
    }
    public function createTestimonial(Request $request){
        $validate = Validator::make($request->all(), [
            'Id_Estudiante' => 'required|integer',
            'Id_Empresa' => 'required|integer',
            'Comentario' => 'required|string|max:255',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }
        $testimonial = Testimonio::create([
            'Id_Estudiante' => $request->Id_Estudiante,
            'Id_Empresa' => $request->Id_Empresa,
            'Comentario' => $request->Comentario,
        ]);
        return response()->json([
            'testimonial' => $testimonial
        ], 201);
    }
    public function getTestimonial($id){
        $testimonial = Testimonio::find($id);
        if (!$testimonial) {
            return response()->json([
                'message' => 'No se encontró el testimonio'
            ], 404);
        }
        return response()->json([
            'testimonial' => $testimonial
        ], 200);
    }
    public function updateTestimonial(Request $request, $id){
        $testimonial = Testimonio::find($id);
        if (!$testimonial) {
            return response()->json([
                'message' => 'No se encontró el testimonio'
            ], 404);
        }
        $validate = Validator::make($request->all(), [
            'Id_Estudiante' => 'required|integer',
            'Id_Empresa' => 'required|integer',
            'Comentario' => 'required|string|max:255',
        ]);
        if ($validate->fails()) {
            return response()->json([
                'errors' => $validate->errors()
            ], 422);
        }
        $testimonial->update($request->all());
        return response()->json([
            'message' => 'Testimonio actualizado correctamente',
            'testimonial' => $testimonial
        ], 200);
    }
    public function deleteTestimonial($id){
        $testimonial = Testimonio::find($id);
        if (!$testimonial) {
            return response()->json([
                'message' => 'No se encontró el testimonio'
            ], 404);
        }
        $testimonial->delete();
        return response()->json([
            'message' => 'Testimonio eliminado correctamente'
        ], 200);
    }

}
