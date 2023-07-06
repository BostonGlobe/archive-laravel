<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;

class ElasticsearchService
{
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST')])
            ->build();
    }

    public function search($keyphrase)
    {
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

        $response = $this->client->search($params);

        return collect($response['hits']['hits'])->pluck('_source');
    }
}
