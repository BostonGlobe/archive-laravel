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
            'index' => env('ELASTICSEARCH_INDEX'),
            'body' => [
                'query' => [
                    'multi_match' => [
                        'query' => $keyphrase,
                        'fields' => ['title^2', 'content'],
                    ],
                ],
            ],
        ];

        $response = $this->client->search($params);
        return $response;
    }

    /**
     * Retrieve a article by URL.
     *
     * @param string $url
     * @return array
     */
    public function getArticleByUrl($url)
    {
        $params = [
            'index' => env('ELASTICSEARCH_INDEX'),
            'body' => [
                'query' => [
                    'match' => [
                        'url.keyword' => '/' . $url,
                    ],
                ],
            ],
        ];

        $response = $this->client->search($params);
        return collect($response['hits']['hits'])->pluck('_source');
    }
}
