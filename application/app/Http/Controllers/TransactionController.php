<?php

namespace App\Http\Controllers;

use App\Http\Requests\TransactionRequest;
use Illuminate\Http\Request;

class TransactionController extends Controller
{
    public function makeTransaction(TransactionRequest $r){
        //TODO implementar lógica da transaction
        //verificar se o sender possui saldo suficiente para a transação. Se sim, procede. Se não, retorna erro para o cliente informando
        //débito da conta do sender
        //crédito na conta do receiver
    }
}
