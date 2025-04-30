<?php

use App\Http\Controllers\RolController;
use App\Http\Controllers\applicationsController;
use App\Http\Controllers\authController;
use App\Http\Controllers\companysController;
use App\Http\Controllers\jobsController;
use App\Http\Controllers\resumeController;
use App\Http\Controllers\studentsController;
use App\Http\Controllers\testimonialsController;
use App\Http\Controllers\usersController;
use App\Http\Middleware\CorsMiddleware;
use App\Http\Middleware\IsUserAuth;
use App\Http\Controllers\multimedia_controller;
use App\Http\Controllers\PersonaController;
use App\Http\Controllers\TelefonoController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\sectorController;
use App\Http\Controllers\CarreraController;
use App\Http\Controllers\ChatbotController;

//Public Routes
Route::post('/register', [authController::class, 'register'])->name('register');
Route::post('/login', [authController::class, 'login'])->name('login');
Route::post('/chatbot', [ChatbotController::class, 'sendMessage'])->name('chatbot.sendMessage');

//Private Routes

Route::middleware([CorsMiddleware::class,IsUserAuth::class])->group(function () {
    Route::get('/user', [authController::class, 'getUser'])->name('user.profile');
    Route::post('/logout', [authController::class, 'logout'])->name('user.logOut');

    //endpoint for user
    Route::get('/users', [usersController::class, 'allUsers'])->name('user.all');
    Route::get('/user/{id}', [usersController::class, 'getUser'])->name('user.get');
    Route::put('/user/{id}', [usersController::class, 'updateUser'])->name('user.update');
    Route::delete('/user/{id}', [usersController::class, 'deleteUser'])->name('user.delete');
    Route::post('/user/register', [authController::class, 'register'])->name('user.register');
    //endpoint for company
    Route::get('/companies', [companysController::class, 'getAlllComapanys'])->name('company.all');
    Route::get('/company/{id}', [companysController::class, 'getCompany'])->name('company.get');
    Route::post('/company-create', [companysController::class, 'createCompany'])->name('company.create');
    Route::put('/company-update/{id}', [companysController::class, 'updateCompany'])->name('company.update');
    Route::delete('/company/{id}', [companysController::class, 'deleteCompany'])->name('company.delete');
    Route::post('/company/{id}/imagenes', [companysController::class, 'uploadImageCompany'])->name('company.uploadImage');
    Route::delete('/company/{id}/imagenes', [companysController::class, 'deleteImageCompany'])->name('company.deleteImage');
    Route::get('/sector/all', [companysController::class, 'getSector'])->name('company.sector');
    Route::delete('/company/{id}/telefonos/{phoneId}', [companysController::class, 'deletePhone'])->name('company.deletePhone');
    Route::post('/company/{id}/status', [companysController::class, 'toggleCompanyStatus'])->name('company.status');
    Route::put('/company/{id}/credentials', [companysController::class, 'updateCredentials'])->name('company.updateCredentials');
    //endpoint for jobs
    Route::get('/jobs', [jobsController::class, 'AllJobs'])->name('job.all');
    Route::get('/job/{id}', [jobsController::class, 'getJob'])->name('job.get');
    Route::post('/job-create', [jobsController::class, 'createJob'])->name('job.create');
    Route::put('/job/{id}', [jobsController::class, 'updateJob'])->name('job.update');
    Route::delete('/job/{id}', [jobsController::class, 'deleteJob'])->name('job.delete');
    Route::post('/job/{id}/imagenes', [jobsController::class, 'job.uploadImage']);
    Route::delete('/job/{id}/imagenes', [jobsController::class, 'job.deleteImage']);
    //endpoint for aplications
    Route::get('/aplications', [applicationsController::class, 'getAllAplications'])->name('aplication.all');
    Route::get('/aplication/{id}', [applicationsController::class, 'getAplication'])->name('aplication.get');
    Route::post('/aplication-create', [applicationsController::class, 'createAplication'])->name('aplication.create');
    Route::put('/aplication/{id}', [applicationsController::class, 'updateAplication'])->name('aplication.update');
    Route::delete('/aplication/{id}', [applicationsController::class, 'deleteAplication'])->name('aplication.delete');
    //endpoint for resumes
    Route::get('/resumes', [resumeController::class, 'getAllResumes'])->name('resume.all');
    Route::get('/resume/{id}', [resumeController::class, 'getResume'])->name('resume.get');
    Route::post('/resume-create', [resumeController::class, 'createResume'])->name('resume.create');
    Route::put('/resume/{id}', [resumeController::class, 'updateResume'])->name('resume.update');
    Route::delete('/resume/{id}', [resumeController::class, 'deleteResume'])->name('resume.delete');
    Route::get('/resume/{id}/configuracion', [resumeController::class, 'getResumeConfiguration'])->name('resume.configuration');
    //endpoint for testimonials
    Route::get('/testimonials', [testimonialsController::class, 'getAllTestimonials'])->name('testimonial.all');
    Route::get('/testimonial/{id}', [testimonialsController::class, 'getTestimonial'])->name('testimonial.get');
    Route::post('/testimonial-create', [testimonialsController::class, 'createTestimonial'])->name('testimonial.create');
    Route::put('/testimonial/{id}', [testimonialsController::class, 'updateTestimonial'])->name('testimonial.update');
    Route::delete('/testimonial/{id}', [testimonialsController::class, 'deleteTestimonial'])->name('testimonial.delete');
    //endpoint for Students
    Route::get('/students', [studentsController::class, 'allStudents'])->name('student.all');
    Route::get('/student/{id}', [studentsController::class, 'getStudent'])->name('student.get');
    Route::post('/student-create', [studentsController::class, 'createStudent'])->name('student.create');
    Route::put('/student/{id}', [studentsController::class, 'updateStudent'])->name('student.update');
    Route::delete('/student/{id}', [studentsController::class, 'deleteStudent'])->name('student.delete');
    //endpoint for multimedia
    Route::get( '/multimedia/{id}', [multimedia_controller::class, 'getMultimedia'])->name('multimedia.get');
    Route::post('/multimedia-create', [multimedia_controller::class, 'createMultimedia'])->name('multimedia.create');
    Route::patch('/multimedia/{id}', [multimedia_controller::class, 'updateMultimedia'])->name('multimedia.update');
    Route::patch('/multimedia/{id}/estado', [multimedia_controller::class, 'updateStateMultimedia'])->name('multimedia.updateState');
    Route::delete('/multimedia/{id}', [multimedia_controller::class, 'deleteMultimedia'])->name('multimedia.delete');
    Route::get('/multimedia-tipo/{tipo}', [multimedia_controller::class, 'getMultimediaByType'])->name('multimedia.getByType');
    //endpoint for sectors
    Route::get('/sectors', [sectorController::class, 'getSectors'])->name('sector.all');
    Route::post('/sector-create', [sectorController::class, 'createSector'])->name('sector.create');
    Route::put('/sector/{id}', [sectorController::class, 'updateSector'])->name('sector.update');
    Route::delete('/sector/{id}', [sectorController::class, 'deleteSector'])->name('sector.delete');

});
Route::get('/up', function () {
    return response()->json([
        'message' => 'Server is up and running'
    ], 200);
});

Route::fallback(function () {
    return response()->json([
        'message' => 'Page Not Found'
    ], 404);
});

// Rutas para gestión de roles
Route::get('/roles', [RolController::class, 'index']);
Route::get('/roles/{id}', [RolController::class, 'show']);
Route::post('/roles', [RolController::class, 'store']);
Route::put('/roles/{id}', [RolController::class, 'update']);
Route::delete('/roles/{id}', [RolController::class, 'destroy']);
Route::post('/roles/assign', [RolController::class, 'assignRolToUser']);

// Rutas para Teléfono
Route::get('/telefonos', [TelefonoController::class, 'index']);
Route::get('/telefonos/{id}', [TelefonoController::class, 'show']);
Route::post('/telefonos', [TelefonoController::class, 'store']);
Route::put('/telefonos/{id}', [TelefonoController::class, 'update']);
Route::delete('/telefonos/{id}', [TelefonoController::class, 'destroy']);

// Ruta para Carrera
Route::middleware(['auth:api'])->group(function () {
    Route::apiResource('carreras', CarreraController::class);
});