<?php

use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\Route;

use function PHPSTORM_META\map;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Route::get('/oie', function(){
    $user = [
        'name' => 'jp2',
        'email' => 'joaopedroarrais2@gmail.com',
        'password' => '123',
        'cpf' => '12067966774',
        'balance' => 1000.25,
        'role' => 'default'
    ];

    $transaction = [
        'value' => 100,
        'sender_id' => '8f0a0898-f76a-4ed6-b892-8668ff92b803',
        'receiver_id' => 'e3ba9153-85b8-4ad4-8935-dc48482c4adb'
    ];

    try{
        //User::with('transactions.sender', 'transactions.receiver')->get();
        //User::create($user);
        //Transaction::create($transaction);
        //return User::all();
        return User::with('receipts')->with('sendings')->get();

    }
    catch(Throwable $e){
        echo $e->getMessage();
    }
   
});