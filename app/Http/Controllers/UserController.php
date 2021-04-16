<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;

class UserController extends Controller
{
    /**
     * Displays all the users that have registered
     *
     * @param Request $request
     * @return User[]|Collection
     */
    public function index(Request $request)
    {
        // displays all the users that have been created so far
        return User::all();
    }
}
