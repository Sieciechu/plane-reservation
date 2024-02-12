<?php

declare(strict_types=1);

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PlaneReservationController extends Controller
{
    public function empty(): View
    {
        return view('empty');
    }
}
