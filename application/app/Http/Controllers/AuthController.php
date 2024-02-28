<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Http\Requests\UserRequest;
use App\Http\Services\UserService;
use Throwable;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct(private UserService $userService)
    {

        $this->middleware('auth:api', ['except' => ['login', 'unauthorized', 'register']]);
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

        /**
     * @OA\Post(
     *     path="/api/auth/register",
     *     operationId="register",
     *     tags={"auth"},
     *     summary="create a user",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *      type="object",
     *      @OA\Property(property="name", type="string", example="John Doe"),
     *      @OA\Property(property="email", type="string", example="johndoe@johndoe.com"),
     *      @OA\Property(property="password", type="string", example="123"),
     *      @OA\Property(property="cpf", type="string", example="23443522017"),
     *      @OA\Property(property="role", type="string", example="default"),
     *   ),
     * ),
     *     @OA\Response(
     *         response=201,
     *         description="Successful operation",
     *         @OA\JsonContent(
     *      type="object",
     *      @OA\Property(property="id", type="string", example="8668ff92b803-f76a-8f0a0898-4ed6-b892"),
     *      @OA\Property(property="value", type="number", example="100.25"),
     *      @OA\Property(property="sender_id", type="string", example="8f0a0898-f76a-4ed6-b892-8668ff92b803"),
     *      @OA\Property(property="receiver_id", type="string", example="e3ba9153-85b8-4ad4-8935-dc48482c4adb"),
     *      @OA\Property(property="created_at", type="string", example="2023-06-25T17:56:59.000000Z"),
     *      @OA\Property(property="updated_at", type="string", example="2023-06-25T17:56:59.000000Z"),
     *   ),
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unathenticated", 
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Invalid data"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Error while fetching data in database"
     *     ),
     * ),
     */
    public function register(UserRequest $r){
        try{
            return $this->userService->createUser($r);
        }catch(Throwable $e){
            return response()->json([
                "error"=>$e->getMessage()
            ], 500);
        }
    }
}
