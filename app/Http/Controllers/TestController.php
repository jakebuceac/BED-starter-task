<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class TestController extends Controller
{
    public function test(Request $request)
    {
        // returns the user details if their token is correct
        return $request->user();
    }
}
