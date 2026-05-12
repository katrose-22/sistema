<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\Gerente\EmpresaGerenteController;
use App\Http\Controllers\Api\Gerente\GerenteDashboardController;
use App\Http\Controllers\Api\Gerente\JefePasanteGerenteController;
use App\Http\Controllers\Api\Gerente\PasantiaGerenteController;
use App\Http\Controllers\Api\Jefe\ActividadJefeController;
use App\Http\Controllers\Api\Pasante\BoletaPasanteController;
use App\Http\Controllers\Api\Pasante\ComentarioPasanteController;
use App\Http\Controllers\Api\Pasante\HojaVidaController;
use App\Http\Controllers\Api\Pasante\InformeFinalPasanteController;
use App\Http\Controllers\Api\Pasante\PasanteDashboardController;
use App\Http\Controllers\Api\Pasante\PasantiaPasanteController;
use App\Http\Controllers\Api\Pasante\PerfilPasanteController;
use App\Http\Controllers\Api\Pasante\SeguimientoPasanteController;
use Illuminate\Support\Facades\Route;

Route::get('/test', function () {
    return response()->json([
        'message' => 'API funcionando correctamente',
    ]);
});

Route::post('/login', [AuthController::class, 'login']);
Route::post('/RegistrarUsuario', [AuthController::class, 'RegistroUsuario']);

Route::middleware('auth:sanctum')->group(function () {

    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/me', [AuthController::class, 'me']);

    Route::prefix('gerente')->group(function () {
        Route::get('/MostrarMiEmpresa', [EmpresaGerenteController::class, 'MostrarMiEmpresa']);
        Route::post('/RegistarMiEmpresa', [EmpresaGerenteController::class, 'RegistarMiEmpresa']);
        Route::put('/empresa', [EmpresaGerenteController::class, 'update']);
        Route::get('/jefes-pasantes', [JefePasanteGerenteController::class, 'index']);
        Route::post('/jefes-pasantes', [JefePasanteGerenteController::class, 'store']);

        Route::get('/pasantias', [PasantiaGerenteController::class, 'index']);
        Route::post('/pasantias', [PasantiaGerenteController::class, 'store']);
        Route::get('/pasantias/{id}', [PasantiaGerenteController::class, 'show']);
        Route::put('/pasantias/{id}', [PasantiaGerenteController::class, 'update']);
        Route::patch('/pasantias/{id}/estado', [PasantiaGerenteController::class, 'cambiarEstado']);
        Route::delete('/pasantias/{id}', [PasantiaGerenteController::class, 'destroy']);

        Route::get('/perfil', [GerenteDashboardController::class, 'perfil']);

        Route::get('/empresa', [EmpresaGerenteController::class, 'show']);
        Route::post('/empresa', [EmpresaGerenteController::class, 'store']);
        Route::put('/empresa', [EmpresaGerenteController::class, 'update']);

        Route::get('/pasantias', [PasantiaGerenteController::class, 'index']);
        Route::post('/pasantias', [PasantiaGerenteController::class, 'store']);
        Route::get('/pasantias/{id}', [PasantiaGerenteController::class, 'show']);
        Route::put('/pasantias/{id}', [PasantiaGerenteController::class, 'update']);
        Route::patch('/pasantias/{id}/estado', [PasantiaGerenteController::class, 'cambiarEstado']);

        Route::get('/jefes-pasantes', [JefePasanteGerenteController::class, 'index']);
    });

    Route::prefix('jefe')->group(function () {
        Route::get('/pasantias', [ActividadJefeController::class, 'pasantiasAsignadas']);

        Route::get('/actividades', [ActividadJefeController::class, 'index']);
        Route::post('/actividades', [ActividadJefeController::class, 'store']);
        Route::get('/actividades/{id}', [ActividadJefeController::class, 'show']);
        Route::put('/actividades/{id}', [ActividadJefeController::class, 'update']);
        Route::delete('/actividades/{id}', [ActividadJefeController::class, 'destroy']);
    });

    Route::prefix('pasante')->group(function () {
        Route::get('/dashboard', [PasanteDashboardController::class, 'index']);

        Route::get('/perfil', [PerfilPasanteController::class, 'show']);
        Route::put('/perfil', [PerfilPasanteController::class, 'update']);

        Route::get('/hoja-vida', [HojaVidaController::class, 'show']);
        Route::post('/hoja-vida', [HojaVidaController::class, 'store']);
        Route::put('/hoja-vida', [HojaVidaController::class, 'update']);

        Route::get('/pasantias', [PasantiaPasanteController::class, 'index']);
        Route::get('/pasantias/{id}', [PasantiaPasanteController::class, 'show']);

        Route::post('/inscripcion/{id_pasantia}', [BoletaPasanteController::class, 'store']);
        Route::get('/boleta', [BoletaPasanteController::class, 'show']);

        Route::get('/actividades', [SeguimientoPasanteController::class, 'actividades']);
        Route::get('/bitacora', [SeguimientoPasanteController::class, 'bitacora']);

        Route::get('/informe-final', [InformeFinalPasanteController::class, 'show']);
        Route::post('/informe-final', [InformeFinalPasanteController::class, 'store']);
        Route::put('/informe-final', [InformeFinalPasanteController::class, 'update']);

        Route::post('/comentarios', [ComentarioPasanteController::class, 'store']);
    });
});
