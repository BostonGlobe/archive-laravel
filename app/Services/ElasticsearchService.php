<?php

namespace App\Services;

use Elastic\Elasticsearch\ClientBuilder;
use Elastic\Elasticsearch\Response\Elasticsearch;
use Http\Promise\Promise;
use InvalidArgumentException;
use Elastic\Transport\Exception\NoNodeAvailableException;
use Elastic\Elasticsearch\Exception\ClientResponseException;
use Elastic\Elasticsearch\Exception\ServerResponseException;

class ElasticsearchService
{
    protected $client;

    public function __construct()
    {
        $this->client = ClientBuilder::create()
            ->setHosts([env('ELASTICSEARCH_HOST')])
            ->build();
    }

    /**
     * Search the Elasticsearch index for articles matching the given keyphrase.
     * @param string $keyphrase
     * @param int $page
     * @param int $size
     * @return Elasticsearch|Promise
     * @throws InvalidArgumentException
     * @throws NoNodeAvailableException
     * @throws ClientResponseException
     * @throws ServerResponseException
     */
    public function search($keyphrase, $page, $size)
    {
        $params = [
            'index' => env('ELASTICSEARCH_INDEX'),
            'body' => [
                'from' => ($page - 1) * $size,
                'size' => $size,
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
