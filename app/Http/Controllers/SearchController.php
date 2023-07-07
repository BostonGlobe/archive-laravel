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

        $searchData = $this->elasticsearchService->search($keyphrase);

        $results = collect($searchData['hits']['hits'])->pluck('_source');

        $totalHits = $searchData['hits']['total']['value'];

        return view('searchresults', compact('results', 'keyphrase', 'totalHits'));
    }
}
