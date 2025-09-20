<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function indexUsers(Request $request)
    {
        $query = User::role('user'); 
        
        if ($request->has('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        $users = $query->get(['id', 'name', 'email']);

        return $this->success($users, 'Users retrieved successfully');
    }
}
