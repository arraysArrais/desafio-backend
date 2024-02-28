<?php

namespace App\Http\Services;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;

class UserService
{
    public function findAll(Request $r)
    {
        $limit = $r->exists('limit') ? $r->limit : null;

        $query = User::query();

        if ($r->filled('id')) {
            $query->where('id', $r->id);
        }

        return $query->simplePaginate($limit);
    }

    public function createUser(UserRequest $r){
        DB::beginTransaction();

        try {
            $user = User::create($r->only(['name','email','password','cpf','role']));
            DB::commit();
            
            return response()->json([
                "message"=>"User created",
                "user" => $user,
            ], 201);
        }catch (Throwable $e) {
            DB::rollback();
            return response()->json([
                "error"=>$e->getMessage()
            ], 500);
        }
    }
}
