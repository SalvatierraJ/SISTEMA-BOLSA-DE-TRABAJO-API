<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Mail\PostulacionTrabajoMail;
use Illuminate\Support\Facades\Mail;
use App\Models\Estudiante;
use App\Models\Multimedia;
use App\Models\Postulacion;
use App\Models\Trabajo;
use Illuminate\Support\Facades\Auth;

use Cloudinary\Api\Upload\UploadApi;

class PostulacionesController extends Controller
{
    public function getPostulaciones()
    {
        $user = Auth::user()->load(['rol', 'personas.estudiantes',]);
        $Estudiante = $user->personas[0]->estudiantes[0]->Id_Estudiante;
        $postulaciones = Postulacion::where('Id_Estudiante',$Estudiante)->get();
        $postulaciones -> load(['trabajo', 'trabajo.empresa']);
        return response()->json(['postulaciones' => $postulaciones, 'message' => 'Postulaciones obtenidas con éxito']);
    }

    public function getCurriculumPDF()
    {
        $user = Auth::user()->load(['rol', 'personas.estudiantes', 'personas.telefonos', 'testimonios']);
        $estudiante = Estudiante::with([
            'persona.usuario.multimedia' => function ($query) {
                $query->where('Tipo', 'Curriculum');
            }
        ])->findOrFail($user->personas[0]->estudiantes[0]->Id_Estudiante);
        $cv = $estudiante->persona->usuario->multimedia->all();
        return response()->json($cv);
    }

    public function postular(Request $request)
    {

        if ($request->hasFile('cv')) {
            $file = $request->file('cv');
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $fileExt = $file->getClientOriginalExtension();

            $response = (new UploadApi())->upload($file->getRealPath(), [
                'folder' => 'CV_Estudiantes',
                'public_id' => $originalName,
                'resource_type' => 'raw',
                'format' => $fileExt
            ]);

            $cvPath = $response['secure_url'];

            Multimedia::create([
                'Nombre' => $cvPath,
                'Id_Usuario' => auth()->user()->Id_Usuario,
                'Tipo' => 'Curriculum',
                'Titulo' => $originalName
            ]);
        } else {
            $cv = Multimedia::where('Id_Multimedia', $request->cvGuardado)->first();
            $cvPath = $cv->Nombre;
        }

        $user = Auth::user()->load(['rol', 'personas.estudiantes']);
        $estudiante = Estudiante::with('persona.telefonos','carreras')->findOrFail($user->personas[0]->estudiantes[0]->Id_Estudiante);
        $trabajo = Trabajo::with('empresa')->findOrFail($request->id_trabajo);
        $postulacion = Postulacion::create([
            'Id_Estudiante' => $estudiante->Id_Estudiante,
            'Id_Trabajo' => $trabajo->Id_Trabajo,
            'Fecha_Postulacion' => now(),
        ])->load('estudiante');
        // Enviar correo
        Mail::to($trabajo->empresa->Correo)->send(
            new PostulacionTrabajoMail($estudiante, $trabajo, $cvPath)
        );

        return response()->json(['message' => 'Postulación enviada con éxito.']);
    }
    public function uploadCurriculum(Request $request)
    {
        $request->validate([
            'curriculum' => 'required|file|mimes:pdf,doc,docx|max:2048',
        ]);

        $user = Auth::user()->load(['rol', 'personas.estudiantes']);

        $file = $request->file('curriculum');
        $filePath = $file->getRealPath();
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $fileExt = $file->getClientOriginalExtension();
        $fileName = pathinfo($originalName, PATHINFO_FILENAME);

        $response = (new UploadApi())->upload($filePath, [
            'folder' => 'CV_Estudiantes',
            'public_id' => $fileName,
            'resource_type' => 'raw',
            'format' => $fileExt,
        ]);


        $cvUrl = $response['secure_url'];

        Multimedia::create([
            'Nombre' => $cvUrl,
            'Id_Usuario' => $user->Id_Usuario,
            'Tipo' => 'Curriculum',
            'Titulo' => $fileName,
        ]);
        return response()->json([
            'message' => 'Curriculum subido con éxito.',
            'cv_url' => $cvUrl
        ]);
    }
}
