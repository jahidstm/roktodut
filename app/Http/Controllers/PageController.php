<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PageController extends Controller
{
    /**
     * Display the blood donation informational landing page.
     */
    public function donateBloodInfo()
    {
        return view('pages.donate');
    }
}
