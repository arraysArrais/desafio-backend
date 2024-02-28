<?php

namespace App\Http\Services;

use App\Models\User;
use Illuminate\Http\Request;
use Throwable;

class UserService
{
    public function findAll(Request $r){
        $limit = $r->exists('limit') ? $r->limit : null;

        $query = User::query();

        if($r->filled('id')){
            $query->where('id', $r->id);
        }

        return $query->simplePaginate($limit);
    }
}
