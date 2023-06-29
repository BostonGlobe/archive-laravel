<?php

namespace App\Services;

use DOMDocument;
use DOMNode;
use DOMXPath;
use Carbon\Carbon;

class HtmlCleanup
{
    private static array $contentIds = [
        'content',
        'articleContent',
        'article',
        'Col1',
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
        'mostPopular',
        'catHeader',
        'blogheadTools',
        'recentPosts',
        'indNav',
        'Col2L',
        'relatedContent',
        'pagination',
        'rightAd',
        'moreList'
    ];

    private static array $classes_to_remove = [
        'leftButtons',
        'share-tools-container',
        'padAll10',
        'commCtDiv',
        'noDot',
        'blackNodot',
        'noDot',
    ];

    /**
     * Extract article text.
     *
     * @param DOMDocument $doc
     * @return DOMNode|null The article text, or null if it could not be found.
     */
    public static function extractArticleText($doc)
    {
        foreach (self::$contentIds as $contentId) {
            $articleText = $doc->getElementById($contentId);
            if ($articleText !== null) {
                return $articleText;
            }
        }

        // If we can't find the article by ID, try to find it by class 'story'.
        // Usually the article is split into multiple divs with this class.
        $divs = $doc->getElementsByTagName('div');
        $articleText = $doc->createElement('div');
        foreach ($divs as $div) {
            if ($div->hasAttribute('class') && $div->getAttribute('class') == 'story') {
                $articleText->appendChild($div);
            }
        }

        return $articleText;
    }

    public static function cleanupHtml($doc)
    {
        // Remove all script tags from the document.
        $scripts = $doc->getElementsByTagName('script');
        // Loop through the DOMNodeList backwards.
        while ($script = $scripts->item(0)) {
            $script->parentNode->removeChild($script);
        }
        // Remove all form tags in the article text, using the same method as above.
        $forms = $doc->getElementsByTagName('form');
        while ($form = $forms->item(0)) {
            $form->parentNode->removeChild($form);
        }

        foreach (self::$ids_to_remove as $item) {
            self::removeItem($doc, $item);
        }

        $childElements = $doc->getElementsByTagName('div'); // get all child elements

        foreach ($childElements as $child) {
            foreach (self::$classes_to_remove as $class) {
                if ($child->hasAttribute('class') && $child->getAttribute('class') == $class) {
                    // found the child element with the specified class
                    $child->parentNode->removeChild($child); // remove it
                }
            }

            // We already removed this, but it was often placed twice in the HTML.
            if ($child->hasAttribute('id') && $child->getAttribute('id') == 'sharetoolContainer') {
                $child->parentNode->removeChild($child); // remove it
            }
        }

        return $doc;
    }

    /**
     * Extract the article title.
     * @param DOMDocument $doc
     * @return string|void
     */
    public static function extractTitle($doc)
    {
        $titleNode = $doc->getElementsByTagName('h1')->item(0);
        if ($titleNode) {
            $title = $titleNode->textContent;
            return trim($title);
        }
    }

    /**
     * Extract a description of the article.
     *
     * @param DOMDocument $doc
     * @return string
     */
    public static function extractDescription($doc)
    {
        $metaTags = $doc->getElementsByTagName('meta');
        foreach ($metaTags as $metaTag) {
            if ($metaTag->getAttribute('http-equiv') == 'Description') {
                return $metaTag->getAttribute('content');
            }
            if ($metaTag->getAttribute('name') == 'Description') {
                return $metaTag->getAttribute('content');
            }
        }

        return '';
    }

    /**
     * Remove item from the DOM.
     *
     * @param DOMDocument $doc
     * @param string $item
     * @return void
     */
    public static function removeItem($doc, $item)
    {
        $item = $doc->getElementById($item);
        if ($item !== null) {
            $item->parentNode->removeChild($item);
        }
    }

    public static function extractDateFromString($string)
    {
        // Define the pattern to match the date in the string
        $pattern = '/\/(\d{4})\/(\d{2})\/(\d{2})\//';

        // Perform regex matching to extract the date parts
        preg_match($pattern, $string, $matches);

        if (count($matches) === 4) {
            // Create a Carbon instance from the extracted date parts
            $year = $matches[1];
            $month = $matches[2];
            $day = $matches[3];

            $date = Carbon::create($year, $month, $day);

            return $date->toDateString();
        }

        return null; // Return null if no date match found
    }

    public static function extractAuthor($doc)
    {
        $author = $doc->getElementByID('byline');

        if ($author && !empty($author->textContent)) {
            return self::extractNameFromByline($author->textContent);
        } else {
            // Create a new DOMXPath object
            $xpath = new DOMXPath($doc);

            // Use XPath query to find all <span> and <p> tags in a single collection
            $query = '//span | //p';
            $nodes = $xpath->query($query);
            foreach ($nodes as $node) {
                if ($node->hasAttribute('class') && $node->getAttribute('class') == 'byline') {
                    $byline = $node->textContent;
                    return self::extractNameFromByline($byline);
                }
            }
        }
    }
    private static function extractNameFromByline($byline)
    {
        // Remove newlines from the input string
        $byline = str_replace(array("\r", "\n", "By "), '', $byline);
        // Trim the string to remove leading and trailing whitespace
        $byline = trim($byline);

        $pattern = '/^(.*?),/';

        $matches = array();

        if (preg_match($pattern, $byline, $matches)) {
            // The name is captured in the first group
            $name = $matches[1];

            // Remove any trailing whitespace
            $name = trim($name);

            return $name;
        }

        return $byline;
    }
}
