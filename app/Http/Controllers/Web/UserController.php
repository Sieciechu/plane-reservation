<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class UserController extends Controller
{
    public function login(): View
    {
        return view('login');
    }

    public function register(): View
    {
        return view('register');
    }
}
