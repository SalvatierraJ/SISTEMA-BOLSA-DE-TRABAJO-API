<?php

namespace App\Swagger;

use OpenApi\Annotations as OA;

/**
 * @OA\Info(
 *     version="1.0.0",
 *     title="API Bolsa de Trabajo",
 *     description="Documentación de la API desarrollada en Laravel para la bolsa de trabajo.",
 *     @OA\Contact(
 *         name="Soporte",
 *         email="soporte@tuapp.com"
 *     )
 * )
 *
 * @OA\Server(
 *     url=L5_SWAGGER_CONST_HOST,
 *     description="Servidor Laravel local"
 * )
 */
class SwaggerDoc {}
