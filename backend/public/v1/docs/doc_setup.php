<?php
/**
 * @OA\Info(
 *     title="API",
 *     description="Hercafe API",
 *     version="1.0",
 *     @OA\Contact(
 *         email="hercafe@gmail.com",
 *         name="Hercafe"
 *     )
 * )
 *
 * @OA\Server(
 *     url="http://localhost/WebDevelopment25/backend",
 *     description="API server"
 * )
 *
 * @OA\SecurityScheme(
 *     securityScheme="ApiKey",
 *     type="apiKey",
 *     in="header",
 *     name="Authentication"
 * )
 */