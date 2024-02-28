<?php

namespace Tests\Feature;


use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;
use Tests\Utils\TestUtils;

class TransactionTest extends TestCase
{
    protected $seed = true; //roda seeders antes de executar o teste
    use RefreshDatabase;

    public function test_transaction_successful()
    {
        // Criação de usuários de teste
        $sender = User::create([
            'balance' => 1000,
            'cpf' => '86940493093',
            'name' => 'Jane Doe',
            'password' => '123',
            'role' => 'default',
            'email' => 'janedoe@janedoe.com'
        ]);
        $receiver = User::create([
            'balance' => 1000,
            'cpf' => '03215082039',
            'name' => 'John Doe',
            'password' => '123',
            'role' => 'lojista',
            'email' => 'johndoe@janedoe.com'
        ]);

        // chamada para o autorizador externo
        $authorized = Http::get('https://run.mocky.io/v3/5794d450-d2e2-4412-8131-73d0293ac1cc');


        if ($authorized->status()) {
            // Dados da transação
            $body = [
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
                'value' => 50.70,
            ];

            $headers = [
                'Authorization' => 'Bearer ' . TestUtils::getJwtToken()
            ];

            // Chamada ao endpoint da própria api
            $response = $this->postJson('/api/transaction', $body, $headers);

            // Verificação da resposta
            $response->assertStatus(201)
                ->assertJson([
                    'message' => 'Transaction completed successfully'
                ]);

            // Verifica se o saldo do remetente foi debitado corretamente
            $this->assertEquals($sender->balance - $body['value'], $sender->fresh()->balance);

            // Verifica se o saldo do destinatário foi creditado corretamente
            $this->assertEquals($receiver->balance + $body['value'], $receiver->fresh()->balance);

            // Verifica se foi criado um registro de transação
            $this->assertDatabaseHas('transactions', [
                'sender_id' => $sender->id,
                'receiver_id' => $receiver->id,
            ]);
        }
    }
}
