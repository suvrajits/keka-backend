<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HelloWorldController extends Controller
{
    public function index()
    {
        return view('hello', ['message' => 'Hello from Herd!']);
    }

    public function kekaWelcomeMessage()
    {
        return view('hello', ['message' => 'Keka Game Coming Soon']);
    }
}
