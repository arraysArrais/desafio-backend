<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\Models\User;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login', 'unauthorized']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */

    /**
     * @OA\Post(
     *     path="/api/auth/login",
     *     operationId="login",
     *     tags={"auth"},
     *     summary="get access-token",
     *     description="JWT Token. Required for all requests.",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *      type="object",
     *      @OA\Property(property="email", type="string", example="admin@admin.com"),
     *      @OA\Property(property="password", type="string", example="123"),
     *   ),
     * ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *         type="object",
     *          @OA\Property(property="access-token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *          @OA\Property(property="token_type", type="string", example="bearer"),
     *          @OA\Property(property="expires_in", type="int", example="3600"),
     *       ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Incorrect username or password", 
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error while fetching data in database"
     *     ),
     * ),
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        $token = auth()->setTTL(60)->attempt($credentials);

        if (!$token) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        return $this->respondWithToken($token);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout()
    {
        auth()->logout();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh()
    {
        return $this->respondWithToken(auth()->refresh());
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token)
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60,
            // 'user' => auth()->user(),
        ]);
    }

    public function verify()
    {
        $user = auth()->user();
        if ($user) {
            return response()->json($user, 200);
        }
    }

    public function revokeToken()
    {
        $user = auth()->user();
        $token = auth()->tokenById($user->id);
        auth()->invalidate();

        return response()->json([
            'user' => $user->name,
            'token' => $token,
            'msg' => 'Token invalidated'
        ], 200);
    }

    public function unauthorized()
    {
        return response()->json([
            'error' => 'Unathenticated..'
        ], 401);
    }
}
