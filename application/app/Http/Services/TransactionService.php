<?php

namespace App\Http\Services;

use App\Http\Requests\TransactionRequest;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Throwable;

class TransactionService
{
    public function __construct(private RabbitmqService $rabbitMQ)
    {
    }

    public function transaction(TransactionRequest $r){
        $sender = User::find($r->get('sender_id'));

        $sufficientFunds = $sender->balance >= $r->get('value');

        //checa por saldo na conta do cliente
        if (!$sufficientFunds) {
            return response()->json([
                "message" => "Insufficient funds"
            ], 400);
        }

        //realiza requisição para autorizador externo passando o payload original
        $authorizedByExternalEntity = Http::post('https://run.mocky.io/v3/5794d450-d2e2-4412-8131-73d0293ac1cc', $r);

        //checa se o autorizador externo permite a operação, considerando que em caso de sucesso é retornado status code 200
        if ($authorizedByExternalEntity->successful()) {
            //inicia a operação na base
            DB::beginTransaction();
            try {
                //debita o valor solicitado da conta do cliente
                $sender->balance = $sender->balance - $r->get('value');
                $sender->save();

                //credita o valor solictado na conta do recebedor
                $receiver = User::find($r->get('receiver_id'));
                $receiver->balance += $r->get('value');
                $receiver->save();

                //cria registro de transaction no banco
                $transaction = Transaction::create($r->toArray());

                DB::commit();
            } catch (Throwable $e) {
                DB::rollback();
                return response()->json(["message" => "Transaction failed"], 500);
            }

            //monta mensagem a ser enviada para a fila
            $msg = [
                "id" => Transaction::latest()->first()->only('id')['id'],
                "value" => $r->get('value'),
                "sender" => User::find($r->get('sender_id'))->only(['name', 'email', 'balance']),
                "receiver" => User::find($r->get('receiver_id'))->only(['name', 'email', 'balance']),
            ];

            //envia a mensagem para a fila configurada no serviço do RabbitMQ
            $this->rabbitMQ->sendMsg(json_encode($msg));

            return response()->json([
                "message" => "Transaction completed successfully",
                "transaction" => $transaction
            ], 201);
        }

        return response()->json(['error' => 'Transaction not allowed by external entity'], 400);
    }

    public function findAll(Request $r){
        $limit = $r->exists('limit') ? $r->limit : null;

        $query = Transaction::query();

        if ($r->filled('sender_id') && $r->filled('receiver_id')) {
            $query->where('sender_id', $r->sender_id)->where('receiver_id', $r->receiver_id);
        }
        if ($r->filled('sender_id')) {
            $query->where('sender_id', $r->sender_id);
        }
        if ($r->filled('receiver_id')) {
            $query->where('receiver_id', $r->receiver_id);
        }

        return $query->simplePaginate($limit);
    }
}
