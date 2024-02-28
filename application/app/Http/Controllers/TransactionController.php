<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use App\Http\Services\TransactionService;
use Illuminate\Http\Request;
use Throwable;

class TransactionController extends Controller
{

    public function __construct(private TransactionService $transactionService)
    {
    }
    /**
     * @OA\Post(
     *     path="/api/transaction/",
     *     security={{"bearerAuth": {}}},
     *     operationId="makeTransaction",
     *     tags={"Transactions"},
     *     summary="create a transaction",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *      type="object",
     *      @OA\Property(property="value", type="number", example="100.25"),
     *      @OA\Property(property="sender_id", type="string", example="8f0a0898-f76a-4ed6-b892-8668ff92b803"),
     *      @OA\Property(property="receiver_id", type="string", example="e3ba9153-85b8-4ad4-8935-dc48482c4adb"),
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
    public function makeTransaction(TransactionRequest $r){
        try {
            return $this->transactionService->transaction($r);
        } catch (Throwable $e) {
            return response()->json([
                "error" => "Internal error",
                "message" => $e->getMessage()
            ], 500);
        }
    }

    /**
     * @OA\Get(
     *     path="/api/transaction/",
     *     security={{"bearerAuth": {}}},
     *     tags={"Transactions"},
     *     summary="retrieve transactions",
     *     operationId="getTransactionsBySenderId",
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
     *         name="sender_id",
     *         in="query",
     *         required=false,
     *         @OA\Schema(
     *             type="string",
     *         )
     *     ),      
     *     @OA\Parameter(
     *         name="receiver_id",
     *         in="query",
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
    public function getTransactions(Request $r){
        try {
            return $this->transactionService->findAll($r);
        } catch (Throwable $e) {
            return response()->json([
                "error" => $e->getMessage()
            ], 500);
        }
    }
}
