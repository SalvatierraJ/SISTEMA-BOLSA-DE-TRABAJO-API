@component('mail::message')
# Postulación para el puesto: {{ $trabajo->Titulo }}

Estimados señores de **{{ $trabajo->empresa->Nombre }}**,

El estudiante **{{ $estudiante->persona->Nombre }} {{ $estudiante->persona->Apellido1 }} {{ $estudiante->persona->Apellido2 ?? ' '   }}**, actualmente inscrito en nuestra institución, se encuentra interesado en postularse para el puesto de trabajo **“{{ $trabajo->Titulo }}”** publicado en nuestra plataforma de bolsa de trabajo.

A continuación, encontrará el enlace al currículum (CV) del postulante:

@component('mail::button', ['url' => $cvPath])
Ver CV del Estudiante
@endcomponent

---

**Datos del postulante:**

- **Nombre:** {{ $estudiante->persona->Nombre }} {{ $estudiante->persona->Apellido1 }} {{ $estudiante->persona->Apellido2 ?? ' ' }}
- **Correo:** {{ $estudiante->persona->Correo }}
- **Carrera:** {{ $estudiante->carreras[0]->Nombre }}
- **Teléfono:** {{ $estudiante->persona->telefonos[0]->Numero }}

---

Agradecemos de antemano su atención y quedamos atentos a cualquier comunicación adicional.

Saludos cordiales,
**Plataforma de Bolsa de Trabajo**
Universidad Tecnológica Privada de Santa Cruz

@endcomponent
