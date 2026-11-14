<?php

namespace App\Http\Controllers;

use App\Models\Service;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function __invoke(): View
    {
        return view('welcome', [
            'services' => Service::sortedByLabel(Service::query()->get(['id', 'name', 'slug'])),
        ]);
    }
}
