<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Elastic\Elasticsearch\ClientBuilder;

class DeleteAllDocsInIndex extends Command
{
    // In Docker container: docker compose exec laravel php artisan delete:alldocs
    protected $signature = 'delete:alldocs';

    protected $description = 'Delete all docs from the Elasticsearch index';

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

        $index = env('ELASTICSEARCH_INDEX');

        $params = [
            'index' => $index,
            'body' => [
                'query' => [
                    'match_all' => new \stdClass()
                ]
            ]
        ];

        // Delete all documents in the index
        try {
            $response = $elasticsearch->deleteByQuery($params);

            // Check the response for success or error
            if (isset($response['deleted']) && $response['deleted'] > 0) {
                $this->info("All documents have been deleted from the index.");
            } else {
                $this->info("No documents were found in the index.");
            }
        } catch (\Exception $e) {
            $this->error('Failed to delete documents: ' . $e->getMessage());
        }
    }
}
