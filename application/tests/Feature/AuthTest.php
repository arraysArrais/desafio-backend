<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AuthTest extends TestCase
{
    protected $seed = true;
    use RefreshDatabase;

    public function test_request_to_login_endpoint_with_valid_credentials_should_return_token(){
        $body = [
            'email' => 'admin@admin.com',
            'password' => '123'
        ];

        $response = $this->postJson('/api/auth/login', $body);

        $response->assertStatus(200)->assertJsonStructure([
            'access_token',
            'token_type',
            'expires_in'
        ]);
    }

    public function test_request_to_login_endpoint_with_invalid_credentials_should_return_401(){
        $body = [
            'email' => 'admin',
            'password' => '123'
        ];

        $response = $this->postJson('/api/auth/login', $body);
        $response->assertStatus(401);
    }

    public function test_request_to_register_endpoint_with_invalid_data_should_return_422(){
        $response = $this->postJson('api/auth/register', [
            "name" => "Teste",
            "email" => "test@test.com",
            "password" => "1234567",
            "cpf" => "12345678912",
            "role" => "teste"
        ]);
        $response->assertStatus(422);
    }

    public function test_request_to_register_endpoint_with_valid_data_should_return_201(){
        $response = $this->postJson('api/auth/register', [
            "name" => "Test",
            "email" => "testuser@testuser.com",
            "password" => "12345678",
            "cpf" => "24252805023",
            "role" => "default"
        ]);
        $response->assertStatus(201);
    }
}
