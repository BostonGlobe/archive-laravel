<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Elastic\Elasticsearch\ClientBuilder;

class CreateElasticSearchIndex extends Command
{
    protected $signature = 'create:esindex';

    protected $description = 'Create an Elasticsearch index';

    public function handle()
    {
        $elasticsearch = ClientBuilder::create()
        ->setHosts([env('ELASTICSEARCH_HOST')])
        ->build();

        // Test the client
        try {
            $info = $elasticsearch->info();
            $clusterName = $info['cluster_name'];

            $this->info('Connected to Elasticsearch cluster: ' . $clusterName);
        } catch (\Exception $e) {
            $this->error('Failed to connect to Elasticsearch: ' . $e->getMessage());
            return;
        }

        // Define the index and mapping
        $params = [
            'index' => env('ELASTICSEARCH_INDEX'),
            'body' => [
                'settings' => [
                    'analysis' => [
                        'char_filter' => [
                            'my_html_stripper' => [
                                'type' => 'html_strip'
                            ]
                        ],
                        'analyzer' => [
                            'sentence' => [
                                'type' => 'custom',
                                'char_filter' => ['my_html_stripper'],
                                'tokenizer' => 'standard'
                            ],
                            'english_stop' => [
                                'type' => 'standard',
                                'stopwords' => '_english_'
                            ],
                            'english_phrase' => [
                                'type' => 'standard'
                            ]
                        ]
                    ]
                ],
                'mappings' => [
                    'properties' => [
                        'article_length' => [
                            'type' => 'long'
                        ],
                        'author' => [
                            'type' => 'text',
                            'fields' => [
                                'keyword' => [
                                    'type' => 'keyword',
                                    'ignore_above' => 256
                                ]
                            ]
                        ],
                        'content' => [
                            'type' => 'text',
                            'analyzer' => 'english_stop',
                            'fields' => [
                                'phrase' => [
                                    'type' => 'text',
                                    'analyzer' => 'english_phrase'
                                ],
                                'keyword' => [
                                    'type' => 'keyword',
                                    'ignore_above' => 256
                                ]
                            ],
                            'term_vector' => 'with_positions_offsets',
                            'store' => true
                        ],
                        'date' => [
                            'type' => 'date'
                        ],
                        'section' => [
                            'type' => 'keyword'
                        ],
                        'title' => [
                            'type' => 'text',
                            'fields' => [
                                'keyword' => [
                                    'type' => 'keyword',
                                    'ignore_above' => 256
                                ]
                            ],
                            'term_vector' => 'with_positions_offsets',
                            'store' => true
                        ],
                        'url' => [
                            'type' => 'text',
                            'fields' => [
                                'keyword' => [
                                    'type' => 'keyword',
                                    'ignore_above' => 256
                                ]
                            ]
                        ]
                    ]
                ]
            ]
        ];

        // Create the index with the modified mapping
        $response = $elasticsearch->indices()->create($params);

        // Check the response for success or error
        if ($response['acknowledged']) {
            $this->info("Index created with modified mapping.");
        } else {
            $this->error("Failed to create index with modified mapping.");
        }
    }
}
