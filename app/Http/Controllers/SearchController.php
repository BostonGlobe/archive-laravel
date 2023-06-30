<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {
        $query = $request->get('query');

        // Perform your search logic here

        return view('search.results', compact('results'));
    }
}
