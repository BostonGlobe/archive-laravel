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

        return View::make('article', [
            'title' => $article['title'],
            'content' => $article['content'],
            'description' => $article['description']
        ])->render();
    }
}
