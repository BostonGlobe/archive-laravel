<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Article;

class ArticleController extends Controller
{
    public function show($path)
    {
        $result = Article::getArticleByUrl($path);

        if ($result === false) {
            abort(404);
        }

        return $result;
    }
}
