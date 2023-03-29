<?php

namespace App\Models;

use DOMDocument;

class Article
{
    public static function findHtmlFile($path)
    {
        if (! str_ends_with($path, '/index.html')) {
            $path .= '/index.html';
        };
    
        $filePath = resource_path('/articles/' . $path);
        
        if (!file_exists($filePath)) {
            abort(404);
        }
    
        $html = file_get_contents($filePath);

        return cache()->remember($path, 120, function () use ($html) {
            return self::reformatFile($html);
        });
    }

    private static function reformatFile($html)
    {
        // converts all special characters to utf-8
        $html = mb_convert_encoding($html, 'HTML-ENTITIES', 'UTF-8');

        // creating new document
        $doc = new DOMDocument('1.0', 'utf-8');

        //turning off some errors
        libxml_use_internal_errors(true);

        // Load the content without adding enclosing html/body tags.
        // Also no doctype declaration.
        $doc->LoadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Remove the shareThis.
        $shareThis = $doc->getElementById('toolsShareThis');
        $shareThis->parentNode->removeChild($shareThis);

        // Remove YahooB.
        $toolsYahooB = $doc->getElementById('toolsYahooB');
        $toolsYahooB->parentNode->removeChild($toolsYahooB);

        // Remove scripts
        $scripts = $doc->getElementsByTagName('script');
        foreach ($scripts as $script) {
            $script->parentNode->removeChild($script);
        }

        // Extract the article text
        $articleText = $doc->getElementById('Col1');

        // Load the template into a new document.
        $template = file_get_contents(resource_path('/views/template.html'));
        $updatedDoc = new DOMDocument('1.0', 'utf-8');
        $updatedDoc->loadHTML($template);

        // Add the article text to the template.
        $content = $updatedDoc->getElementById('content');
        $content->appendChild($updatedDoc->importNode($articleText, true));

        // Return the article in the template.
        return $updatedDoc->saveHTML();
    }
}
