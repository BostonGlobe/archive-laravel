<?php

namespace App\Models;

use DOMDocument;
use Illuminate\Contracts\Container\BindingResolutionException;
use Illuminate\Support\Facades\View;

class Article
{
    /**
     * Resolve the HTML file path, making sure it ends with "/index.html".
     *
     * @param string $path
     * @return string The resolved HTML file path.
     */
    private static function resolveHtmlFilePath($path)
    {
        if (! str_ends_with($path, '/index.html')) {
            $path .= '/index.html';
        }

        return resource_path('/html/' . $path);
    }

    /**
     * Fetch the contents of the HTML file, applying some formatting.
     *
     * The contents are cached for 2 minutes.
     *
     * @param string $path
     * @return string|false The formatted HTML file contents, or false if the file does not exist.
     */
    public static function fetchFormattedHtmlFile($path)
    {
        $filePath = self::resolveHtmlFilePath($path);

        if (! file_exists($filePath)) {
            return false;
        }

        $html = file_get_contents($filePath);

        return cache()->remember($path, 1, function () use ($html) {
            return self::reformatFile($html);
        });
    }

    /**
     * Reformat the content from the old file and insert it into an HTML5 template and remove obsolete elements.
     *
     * @param mixed $html
     * @return string The formatted HTML file contents.
     */
    private static function reformatFile($html)
    {
        // Convert all special characters to utf-8
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        // Create a new document
        $doc = new DOMDocument('1.0', 'utf-8');

        // Turn off some errors
        libxml_use_internal_errors(true);

        // Load the content without adding enclosing html/body tags.
        // Also no doctype declaration.
        $doc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);
        $metaTags = $doc->getElementsByTagName('meta');
        $description = '';
        foreach ($metaTags as $metaTag) {
            if ($metaTag->getAttribute('http-equiv') == 'Description') {
                $description = $metaTag->getAttribute('content');
                break;
            }
        }
        if ($description == '') {
            foreach ($metaTags as $metaTag) {
                if ($metaTag->getAttribute('name') == 'Description') {
                    $description = $metaTag->getAttribute('content');
                    break;
                }
            }
        }

        // Extract the article text
        $articleText = $doc->getElementById('Col1');

        if ($articleText === null) {
            $articleText = $doc->getElementById('articleContent');
        }
        if ($articleText === null) {
            $articleText = $doc->getElementById('article');
        }
        if ($articleText === null) {
            abort(404);
        }
        // Remove all script tags in the article text
        $scripts = $articleText->getElementsByTagName('script');
        // Yes this is a non-standard loop, but it's a good way to remove all elements from a DOMNodeList.
        while ($script = $scripts->item(0)) {
            $script->parentNode->removeChild($script);
        }

        // Remove all form tags in the article text, using the same method as above.
        $forms = $articleText->getElementsByTagName('form');
        while ($form = $forms->item(0)) {
            $form->parentNode->removeChild($form);
        }

        $h1 = $articleText->getElementsByTagName('h1');

        // Load the template into a new document.
        return View::make('template', [
            'title' => $h1[0]->nodeValue,
            'content' => $doc->saveHTML($articleText),
            'description' => $description
        ])->render();
    }
}
