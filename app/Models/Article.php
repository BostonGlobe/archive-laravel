<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Support\Facades\View;
use App\Services\ElasticsearchService;

class Article
{
    /**
     * Retrieve a article by URL.
     *
     * @param string $url
     * @return array
     */
    public static function getArticleByUrl($url)
    {
        $es = new ElasticsearchService();
        $articles = $es->getArticleByUrl($url);

        $article = $articles[0] ?? null;

        if ($article === null) {
            abort(404);
        }
        return View::make('article', [
            'title' => $article['title'],
            'content' => $article['content'],
            'description' => $article['description']
        ])->render();
    }
}
