<?php

namespace App\Http\Controllers;

use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Http\Request;

class SearchController extends Controller
{
    public function search(Request $request)
    {

        $keyphrase = $request->input('s');

        $client = ClientBuilder::create()
        ->setHosts([env('ELASTICSEARCH_HOST')])
        ->build();

        $params = [
            'index' => env('ELASTICSEARCH_INDEX'), // Replace with your Elasticsearch index name
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $keyphrase,
                        'fields' => ['title^3', 'description^2', 'content'],
                    ],
                ],
            ],
        ];

        $response = $client->search($params);

        $results = collect($response['hits']['hits'])->pluck('_source');

        return view('searchresults', compact('results', 'keyphrase'));
    }
}
