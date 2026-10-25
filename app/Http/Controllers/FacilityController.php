<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class FacilityController extends Controller
{
    /**
     * Placeholder for the facility directory.
     * The listing, pagination, and filters are built in later issues.
     */
    public function index(): View
    {
        return view('facilities.index');
    }
}
