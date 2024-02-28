<?php

namespace App\Http\Controllers;

use App\Http\Services\UserService;
use Illuminate\Http\Request;
use Throwable;

class UserController extends Controller
{
    public function __construct(private UserService $userService)
    {
    }

    /**
     * @OA\Get(
     *     path="/api/user/",
     *     security={{"bearerAuth": {}}},
     *     tags={"Users"},
     *     summary="retrieve users",
     *     operationId="getAll",
     *     @OA\Parameter(
     *         name="limit",
     *         in="query",
     *         description="limit of records per page",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="page",
     *         in="query",
     *         description="page which you want to navigate to",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),
     *     @OA\Parameter(
     *         name="id",
     *         in="query",
     *         description="user id",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),      
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unathenticated"
     *     ),
     *     @OA\Response(
     *         response=500,
     *         description="Internal Error"
     *     )
     * )
     */
    public function getAll(Request $r){
        try{
            return $this->userService->findAll($r);
        }
        catch(Throwable $e){
            return response()->json([
                "error"=>$e->getMessage()
            ], 500);
        }
    }
}
