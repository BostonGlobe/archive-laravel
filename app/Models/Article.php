<?php

declare(strict_types=1);

namespace App\Models;

use DOMDocument;
use DOMNode;
use Illuminate\Support\Facades\View;
use App\Services\HtmlCleanup;

class Article
{
    private static string $title = '';

    private static string $description = '';

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
        if (str_ends_with($path, '/')) {
            $path .= 'index.html';
        }

        return 'https://archive.boston.com/' . $path;
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

        // The last param is the maximum length of the file that we will fetch.
        // This is included for security. It is 5 times a typical article length.
        $html = @file_get_contents($filePath, false, null, 0, 170000);

        if (! $html) {
            return false;
        }

        return cache()->remember($path, 1, function () use ($html) {
            return self::reformatFile($html);
        });
    }


    /**
     * Reformat the content from the old file and insert it into an HTML5 template and remove obsolete elements.
     *
     * @param string $html
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

        // Extract the title
        self::$title = HtmlCleanup::extractTitle(self::$doc) ?? 'Boston.com';

        // Extract Description
        self::$description = HtmlCleanup::extractDescription(self::$doc);

        self::$doc = HtmlCleanup::cleanupHtml(self::$doc);

        // Extract the article text
        self::$articleText = HtmlCleanup::extractArticleText(self::$doc);

        if (self::$articleText !== null) {
            // Load the template into a new document.
            return View::make('article', [
            'title' => self::$title,
            'content' => self::$doc->saveHTML(self::$articleText),
            'description' => self::$description
            ])->render();
        } else {
            abort(404);
        }
    }
}
