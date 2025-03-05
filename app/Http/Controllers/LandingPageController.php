<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Warta;
use App\Models\Gallery;

class LandingPageController extends Controller
{
    public function showLandingPage()
    {
        $latestWarta = Warta::latest()->first();
        $fileName = $latestWarta ? $latestWarta->file_name : null;

        $images = Gallery::all();

        return view('landingpage.landingpage', compact('fileName', 'images'));
    }
}