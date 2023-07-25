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


        File::lines($filepath)->each(function ($url) use ($elasticsearch) {

            // Prepare the search query to check if the file has already been indexed.
            $params = [
                'index' => env('ELASTICSEARCH_INDEX'),
                'body' => [
                    'query' => [
                        'match' => [
                            'url.keyword' => trim($url)
                        ]
                    ]
                ]
            ];

            $response = $elasticsearch->search($params);

            // If the file has already been indexed, skip it.
            if (($response['hits']['total']['value'] > 0)) {
                $this->info('Already indexed ' . $url);
                return;
            } else {
                $html = @file_get_contents('https://archive.boston.com/' . $url, false, null, 0, 170000);

                // Convert all special characters to utf-8.
                $html = iconv('ISO-8859-1', 'UTF-8//IGNORE', $html);

                // Create a new document.
                $doc = new DOMDocument('1.0', 'UTF-8');

                // Turn off errors.
                libxml_use_internal_errors(true);

                // Load the content without adding enclosing html/body tags.
                // Also no doctype declaration.
                $doc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

                $title = HtmlCleanup::extractTitle($doc) ?? 'Boston.com';

                $description = HtmlCleanup::extractDescription($doc);

                $date = HtmlCleanup::extractDateFromString($url);

                $doc = HtmlCleanup::cleanupHtml($doc);

                $author = HtmlCleanup::extractAuthor($doc);

                $section = HtmlCleanup::extractFirstDirectory($url);

                // Extract the article text.
                $articleText = HtmlCleanup::extractArticleText($doc);

                $contentLength = strlen(trim($articleText->textContent));

                $this->info('Content length: ' . $contentLength);

                if ($contentLength < 300) {
                    $this->info('Not enough content to index ' . $url);
                    return;
                }
                $this->info('Indexing: ' . $url);

                $htmlContent = $doc->saveHTML($articleText);

                $elasticsearch->index([
                    'index' => env('ELASTICSEARCH_INDEX'),
                    'body' => [
                        'url' => $url,
                        'title' => $title,
                        'description' => $description,
                        'date' => $date,
                        'author' => $author,
                        'section' => $section,
                        'article_length' => $contentLength,
                        'content' => $htmlContent,
                    ],
                ]);
            }
        });
    }
}
