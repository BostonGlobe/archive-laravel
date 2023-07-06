<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ElasticsearchService;

class SearchController extends Controller
{
    protected $elasticsearchService;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        $this->elasticsearchService = $elasticsearchService;
    }

    public function search(Request $request)
    {
        $request->validate([
            's' => 'required|max:255',
        ]);

        $keyphrase = $request->input('s');
        $results = $this->elasticsearchService->search($keyphrase);

        return view('searchresults', compact('results', 'keyphrase'));
    }
}
