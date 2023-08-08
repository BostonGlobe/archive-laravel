<?php

/**
 * Search Model.
 * Returns a paginated collection of search results.
 * Each result contains an excerpt with the search terms highlighted.
 */

declare(strict_types=1);

namespace App\Models;

use App\Services\ElasticsearchService;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\Validator;

class Search
{
    protected $elasticsearchService;

    public function __construct(ElasticsearchService $elasticsearchService)
    {
        $this->elasticsearchService = $elasticsearchService;
    }

    public function doSearch($requestParams)
    {
        // Validate the request parameters.
        $validator = Validator::make($requestParams, [
            's' => 'required|max:255',
            'page' => 'integer|min:1',
            'size' => 'integer|min:1|max:50'
        ]);

        // If the validation fails, return the errors.
        if ($validator->fails()) {
            return ['errors' => $validator->errors()];
        }

        // Get the keyphrase, page, and size from the request.
        $keyphrase = $requestParams['s'];
        $page      = $requestParams['page'] ?? 1;
        $size = $requestParams['size'] ?? 10;

        // Search the index.
        $searchData = $this->elasticsearchService->search($keyphrase, $page, $size);

        $results = collect($searchData['hits']['hits'])->pluck('_source');

        // Create an excerpt for each result, with the search terms highlighted.
        $results->transform(function ($item, $key) use ($searchData) {
            // dd($searchData['hits']['hits'][$key]);
            $highlight = isset($searchData['hits']['hits'][$key]['highlight']) ?
                $searchData['hits']['hits'][$key]['highlight']['content'] : null;

            $item['excerpt'] = '';

            if ($highlight) {
                foreach ($highlight as $fragment) {
                    // Remove paragraphs because they if removed by strip_tags, they ended up with no space between sentences.
                    $fragment = preg_replace('#<p[^>]*>#i', ' ', html_entity_decode($fragment));
                    $fragment = str_replace('</p>', ' ', html_entity_decode($fragment));

                    // Remove all tags except <strong>.
                    $item['excerpt' ] .= strip_tags(html_entity_decode($fragment), ['<strong>']) . '… ';
                }
            }


            // if the excerpt is less than 200 characters, create a new excerpt from the content.
            if (strlen($item['excerpt']) < 200) {
                $item['excerpt'] = substr(strip_tags(html_entity_decode($item['content'])), 0, 300) . '…';
            }

            // Format the date, if it exists.
            if (isset($item['date'])) {
                $date = new \DateTime($item['date']);
                $item['date'] = $date->format('M d, Y');
            }

            if ($item['section'] === 'ae') {
                $item['section'] = 'Arts & Entertainment';
            }

            return $item;
        });

        // Get total hits.
        $totalHits = $searchData['hits']['total']['value'];

        // Paginate the results
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

        // return the results, keyphrase, and total hits in an array.
        return ['results' => $results, 'keyphrase' => $keyphrase, 'totalHits' => $totalHits];
    }
}
