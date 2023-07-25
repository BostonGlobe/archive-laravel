<?php

/**
 * Article Model.
 */

declare(strict_types=1);

namespace App\Models;

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

        return $articles[0] ?? null ;
    }
}
