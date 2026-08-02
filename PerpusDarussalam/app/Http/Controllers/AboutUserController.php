<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AboutUserController extends Controller
{
    public function index()
    {
        return view('layouts.pages.users.tentang_kami');
    }
}