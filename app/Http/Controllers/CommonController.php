<?php

namespace App\Http\Controllers;

use App\Http\Resources\UserResource;
use Illuminate\Http\Request;

class CommonController extends Controller
{
    public function profile(){

        return response()->json([
            'status' => true,
            'user' => new UserResource(auth()->user()),
        ]);
    }
}
