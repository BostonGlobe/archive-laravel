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

        // Test the client
        try {
            $info = $this->client->info();
            $clusterName = $info['cluster_name'];
        } catch (\Exception $e) {
            throw new \Exception('Failed to connect to Elasticsearch: ' . $e->getMessage());
        }
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
        $queryArray = $this->formatQueryArray($keyphrase);

        $params = [
            'index' => env('ELASTICSEARCH_INDEX'),
            'body' => [
                'from' => ($page - 1) * $size,
                'size' => $size,
                'query' => [
                    'function_score' => [
                        'query' => $queryArray,
                        'functions' => [
                            [
                            'filter' => [ 'range' => [ 'article_length' => [ 'gt' => 1000 ] ] ],
                            'weight' => 2
                            ]
                        ],
                        'boost_mode' => 'multiply'
                    ],
                ],
                'fields' => ['title', 'url'],
                'highlight' => [
                    'pre_tags' => ['<strong class="highlight">'],
                    'post_tags' => ['</strong>'],
                    'type' => 'plain',
                    'number_of_fragments' => 3,
                    'encoder' => 'html',
                    'fields' => [
                        'content.phrase' => [
                            'fragment_size' => 180,
                            'no_match_size' => 180,
                        ],
                        'content' => [
                            'fragment_size' => 180,
                            'no_match_size' => 180,
                        ],
                    ],
                ],
            ],
        ];

        return $this->client->search($params);
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

        // if response code is not 200, return an empty array
        if ($response->getStatusCode() !== 200) {
            return [];
        }

        return collect($response['hits']['hits'])->pluck('_source');
    }

    /**
     * Format a search query for Elasticsearch.
     *
     * @param string $keyphrase
     * @return array
     */
    private function formatQueryArray($keyphrase)
    {
        // Check for quotes for phrase matching
        $queryType = $this->hasQuotes($keyphrase) !== false ? 'phrase' : 'best_fields';

        // Build the base query
        $queryArray = [
            'multi_match' => [
                'query' => $keyphrase,
                'type' => $queryType,
            ],
        ];

        // Add fuzziness and AND operator if it's a 'best_fields' query
        if ($queryType === 'best_fields') {
            $queryArray['multi_match']['fields'] = ['title^2', 'content'];
            $queryArray['multi_match']['fuzziness'] = 'AUTO';
            $queryArray['multi_match']['operator'] = 'AND';
        }
        // Otherwise, it's a 'phrase' query
        else {
            $queryArray['multi_match']['fields'] = ['title^2', 'content.phrase'];
        }
        return $queryArray;
    }


    /**
     * Check a $keyphrase for quotes.
     * @param string $keyphrase
     * @return bool
     */
    private function hasQuotes($keyphrase)
    {
        $doubleQuotesCount = substr_count($keyphrase, '"');
        $singleQuotesCount = substr_count($keyphrase, "'");

        return ($doubleQuotesCount >= 2 || $singleQuotesCount >= 2);
    }
}
