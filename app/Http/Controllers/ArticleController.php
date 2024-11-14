<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Article;
use Illuminate\Support\Facades\View;

class ArticleController extends Controller
{
    public function show($path)
    {
        $article = Article::getArticleByUrl($path);

        if (!$article) {
            abort(404);
        }

        $filteredContent = $this->filterContent($article['content']);

        return View::make('article', [
            'title' => $article['title'],
            'content' => $filteredContent,
            'description' => $article['description']
        ])->render();
    }

    /**
     * Filter the content, to change links to boston.com to archive.boston.com.
     */
    protected function filterContent($content)
    {
        // Use regex to match //boston.com or //www.boston.com
        $pattern = '/\/\/(www\.)?boston\.com/';
        $replacement = '//archive.boston.com';
        return preg_replace($pattern, $replacement, $content);
    }
}
