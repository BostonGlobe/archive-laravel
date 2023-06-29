<?php

namespace App\Console\Commands;

use DOMDocument;
use Illuminate\Console\Command;
use Elastic\Elasticsearch\ClientBuilder;
use Illuminate\Support\Facades\File;
use App\Services\HtmlCleanup;

class IndexHtmlFiles extends Command
{
    protected $signature = 'index:html {filepath}';

    protected $description = 'Index HTML files into Elasticsearch';

    public function handle()
    {
        $filepath = $this->argument('filepath');

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


        File::lines($filepath)->each(function ($path) use ($elasticsearch) {

            // Prepare the search query
            $params = [
                'index' => 'archive-bdc',
                'body' => [
                    'query' => [
                        'term' => [
                            'path.keyword' => $path
                        ]
                    ]
                ]
            ];

            $response = $elasticsearch->search($params);

            if (($response['hits']['total']['value'] > 0)) {
                $this->info('Already indexed ' . $path);
                return;
            } else {
                $html = file_get_contents('https://archive.boston.com/' . $path, false, null, 0, 170000);

                if (! $html || str_starts_with($html, 'This page has expired.')) {
                    $this->info('This is expired: ' . $path);
                    return;
                }

                $this->info('Indexing ' . $path);

                // Convert all special characters to utf-8
                $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

                // Create a new document
                $doc = new DOMDocument('1.0', 'utf-8');

                // Turn off some errors
                libxml_use_internal_errors(true);

                // Load the content without adding enclosing html/body tags.
                // Also no doctype declaration.
                $doc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

                $title = HtmlCleanup::extractTitle($doc) ?? 'Boston.com';

                $description = HtmlCleanup::extractDescription($doc);

                $date = HtmlCleanup::extractDateFromString($path);

                $doc = HtmlCleanup::cleanupHtml($doc);

                $author = HtmlCleanup::extractAuthor($doc);

                // Extract the article text
                $articleText = HtmlCleanup::extractArticleText($doc);

                $elasticsearch->index([
                    'index' => 'archive-bdc',
                    'body' => [
                        'path' => $path,
                        'title' => $title,
                        'description' => $description,
                        'date' => $date,
                        'author' => $author,
                        'content' => $doc->saveHTML($articleText),
                    ],
                ]);
            }
        });
    }
}
