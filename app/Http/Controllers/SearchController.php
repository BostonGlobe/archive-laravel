<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\ElasticsearchService;
use Illuminate\Pagination\LengthAwarePaginator;

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
            'page' => 'integer|min:1',
            'size' => 'integer|min:1|max:50'
        ]);

        $keyphrase = $request->input('s');

        $page = $request->input('page', 1);

        $size = $request->input('size', 10);  // Default and maximum size to 10

        $searchData = $this->elasticsearchService->search($keyphrase, $page, $size);

        $results = collect($searchData['hits']['hits'])->pluck('_source');

        $totalHits = $searchData['hits']['total']['value'];

        // Manual pagination
        $results = new LengthAwarePaginator(
            $results,
            $totalHits,
            $size,
            $page,
            [
                'path' => route('search'),
                'query' => [
                    's' => $keyphrase,
                ]
            ]
        );
        $results->appends(['s' => $keyphrase])->links();

        return view('searchresults', compact('results', 'keyphrase', 'totalHits'));
    }
}
