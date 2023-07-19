<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Search;

class SearchController extends Controller
{
    protected $search;

    public function __construct(Search $search)
    {
        $this->search = $search;
    }

    public function search(Request $request) 
    {
        $response = $this->search->doSearch($request->all());

        if (isset($response['errors'])) {
            // Handle the error response, maybe redirect back with the errors.
        }

        return view('searchresults', [
            'results' => $response['results'], 
            'keyphrase' => $response['keyphrase'], 
            'totalHits' => $response['totalHits']
        ]);
    }
}
