<?php

declare(strict_types=1);

namespace App\Models;

use DOMDocument;
use DOMNode;
use Illuminate\Support\Facades\View;

class Article
{
    private static string $title = '';
    private static array $contentIds = [
        'Col1',
        'articleContent',
        'article',
    ];

    private static array $ids_to_remove = [
        'sharetoolContainer',
        'toolsShareThis',
        'toolsYahooB',
        'bdc_emailWidget',
        'articleFootTools',
        'bdc_shareButtons',
        'tools',
        'informBox',
        'articleMoreLinksI',
        'subCont',
    ];

    private static DOMDocument $doc;

    private static DOMNode | null $articleText;
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
     * Extract article text.
     *
     * @return DOMNode|null The article text, or null if it could not be found.
     */
    private static function extractArticleText($doc)
    {
        foreach (self::$contentIds as $contentId) {
            $articleText = self::$doc->getElementById($contentId);
            if ($articleText !== null) {
                return $articleText;
            }
        }

        // If we couldn't find the article by ID, try to find it by class 'story'.
        // Usually the article is split into multiple divs with this class.
        $divs = self::$doc->getElementsByTagName('div');
        $articleText = self::$doc->createElement('div');
        foreach ($divs as $div) {
            if ($div->hasAttribute('class') && $div->getAttribute('class') == 'story') {
                $articleText->appendChild($div);
            }
        }

        return $articleText;
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
        self::$doc = new DOMDocument('1.0', 'utf-8');

        // Turn off some errors
        libxml_use_internal_errors(true);

        // Load the content without adding enclosing html/body tags.
        // Also no doctype declaration.
        self::$doc->loadHTML($html, LIBXML_HTML_NOIMPLIED | LIBXML_HTML_NODEFDTD);

        // Remove all script tags from the document.
        $scripts = self::$doc->getElementsByTagName('script');
        // Loop through the DOMNodeList backwards.
        while ($script = $scripts->item(0)) {
            $script->parentNode->removeChild($script);
        }
        // Remove all form tags in the article text, using the same method as above.
        $forms = self::$doc->getElementsByTagName('form');
        while ($form = $forms->item(0)) {
            $form->parentNode->removeChild($form);
        }

        $metaTags = self::$doc->getElementsByTagName('meta');

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

        // Get the h1 tag for the title.
        $titleNode = self::$doc->getElementsByTagName('h1')->item(0);
        if ($titleNode) {
            self::$title = self::$doc->getElementsByTagName('h1')->item(0)->textContent;
        }

        foreach (self::$ids_to_remove as $item) {
            self::removeItem(self::$doc, $item);
        }

        $childElements = self::$doc->getElementsByTagName('div'); // get all child elements

        foreach ($childElements as $child) {
            if ($child->hasAttribute('class') && $child->getAttribute('class') == 'leftButtons') {
                // found the child element with the specified class
                $child->parentNode->removeChild($child); // remove it
            }
            // We already removed this, but it was often placed twice in the HTML.
            if ($child->hasAttribute('id') && $child->getAttribute('id') == 'sharetoolContainer') {
                $child->parentNode->removeChild($child); // remove it
            }
        }

        // Extract the article text
        self::$articleText = self::extractArticleText(self::$doc);

        if (self::$articleText !== null) {
            // Load the template into a new document.
            return View::make('layouts.template', [
            'title' => self::$title,
            'content' => self::$doc->saveHTML(self::$articleText),
            'description' => $description
            ])->render();
        } else {
            abort(404);
        }
    }

    /**
     * Remove item from the DOM.
     */
    private static function removeItem($doc, $item)
    {
        $item = $doc->getElementById($item);
        if ($item !== null) {
            $item->parentNode->removeChild($item);
        }
    }
}
