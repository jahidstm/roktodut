<?php

namespace App\Http\Controllers;

class PwaController extends Controller
{
    public function offline()
    {
        return view('pwa.offline');
    }
}
