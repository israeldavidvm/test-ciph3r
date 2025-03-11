<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Exception;

/**
 * @OA\SecurityScheme(
 *     securityScheme="sanctum",
 *     type="http",
 *     scheme="bearer",
 *     description="Enter token in format (Bearer <token>) (Ejemplo: Bearer 2|Cfz3yDjKqUh55AUI6I9nQQv6MEHsEqQvJToMDnJ7e7c8478a)",
 *     name="Authorization",
 *     in="header"
 * )
 */
class AuthController extends Controller
{
    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     tags={"Auth"},
     *     summary="Inicia sesión.",
     *     description="Inicia sesión con las credenciales del usuario.",
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos de autenticación",
     *         @OA\JsonContent(
     *             @OA\Property(property="email", type="string"),
     *             @OA\Property(property="password", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Respuesta satisfactoria (OK)",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string"),
     *             @OA\Property(property="access_token", type="string"),
     *             @OA\Property(property="type_token", type="string")
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Entidad no procesable (Unprocessable Entity)",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string")
     *         )
     *     )
     * )
     */
    public function login(Request $request): JsonResponse    {
        $credentials = $request->only('email', 'password');

        if (!Auth::attempt($credentials)) {
            return response()->json(['message' => 'No se pudo iniciar sesión, tus datos son incorrectos'], 422);
        }

        $token = $request->user()->createToken('API Token')->plainTextToken;

        return response()->json([
            'message' => "Inicio de sesión exitoso",
            'userData' => auth()->user(),
            'access_token' => $token,
            'type_token' => "Bearer",
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/me",
     *     tags={"Auth"},
     *     summary="Recuperar datos del usuario autenticado.",
     *     description="Devuelve la información del usuario autenticado.",
     *     security={{"sanctum": {}}},  
     *     @OA\Response(
     *         response=200,
     *         description="Respuesta satisfactoria (OK)",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Se logró recuperar los datos del usuario de forma satisfactoria."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado (Unauthorized)",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No autorizado.")
     *         )
     *     )
     * )
     */
    public function me(): JsonResponse    {
        return response()->json([
            "message" => "Se logró recuperar los datos del usuario de forma satisfactoria",
            "userData" => Auth::user()
        ], 200);
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     tags={"Auth"},
     *     summary="Cerrar sesión.",
     *     description="Cierra la sesión actual del usuario.",
     *     security={{"sanctum": {}}},  
     *     @OA\Response(
     *         response=200,
     *         description="Respuesta satisfactoria (OK)",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="Sesión cerrada con éxito."
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="No autorizado (Unauthorized)",
     *         @OA\JsonContent(
     *             @OA\Property(
     *                 property="message",
     *                 type="string",
     *                 example="No autorizado."
     *             )
     *         )
     *     )
     * )
     */
    public function logout(Request $request): JsonResponse    {
        try {
            // Eliminar el token actual
            $request->user()->currentAccessToken()->delete();
            
            return response()->json(['message' => 'Sesión cerrada con éxito'], 200);
        } catch (Exception $e) {
            // Manejar la excepción y devolver un mensaje de error
            return response()->json(['message' => 'Error al cerrar sesión: ' . $e->getMessage()], 500);
        }
    }
    /**
     * @OA\Post(
     *     path="/api/v1/auth/register",
     *     tags={"Auth"},
     *     summary="Crear un nuevo usuario.",
     *     description="Crea un nuevo usuario en la base de datos.",
     *     security={{"sanctum": {}}},  
     *     @OA\RequestBody(
     *         required=true,
     *         description="Datos del nuevo usuario",
     *         @OA\JsonContent(
     *             @OA\Property(property="name", type="string", example="John Doe"),
     *             @OA\Property(property="email", type="string", example="user@example.com"),
     *             @OA\Property(property="password", type="string", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Crear un nuevo usuario (Created)",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="Usuario creado con éxito."),
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Entidad no procesable (Unprocessable Entity)",
     *         @OA\JsonContent(
     *             @OA\Property(property="message", type="string", example="No se pudo crear el usuario, revise los datos ingresados.")
     *         )
     *     )
     * )
     */
    public function register(Request $request): JsonResponse    {
        $request['action'] = 'create';

        User::validateAttributes($request->all());

        $user = new User();
        $user->initAttributes($request->all());
        $user->save();

        return response()->json([
            'message' => 'User  created successfully',
            'data' => $user
        ]);
    }
}