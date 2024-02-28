<?php 

namespace Tests\Utils;

use Illuminate\Http\Request;
use Illuminate\Contracts\Http\Kernel;

 class TestUtils{
    public static function getJwtToken()
    {
        $body = [
            'email' => 'admin@admin.com',
            'password' => '123'
        ];

        $response = app()->make(Kernel::class)->handle(Request::create('/api/auth/login', 'POST', $body));

        $content = $response->getContent();
        $data = json_decode($content, true);

        return $data['access_token'];
    }
}