<?php

namespace App\Services;

use DOMDocument;
use DOMNode;
use DOMXPath;
use Carbon\Carbon;

class HtmlCleanup
{
    private static array $contentIds = [
        'articleContent',
        'article',
        'Col1',
        'mainContent',
        'content',
    ];

    private static array $contentClasses = [
        'mainContent',
        'story',
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
        'proquestBox',
        'globeSub',
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
        'moreList',
        'nonSub',
        'nextIn',
        'outBrain',
        'articleComments',
        'commLoginForm',
        'Comments_Container1wrap',
        'pPolicy',
        'wait',
        'adContainer',
    ];

    private static array $classes_to_remove = [
        'leftButtons',
        'share-tools-container',
        'padAll10',
        'commCtDiv',
        'hideMe',
        'noDot',
        'blackNodot',
        'noDot',
        'toolsMain',
        'emailLinks'
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
            foreach (self::$contentClasses as $contentClass) {
                if ($div->hasAttribute('class') && $div->getAttribute('class') == $contentClass) {
                    $articleText->appendChild($div);
                    break;
                }
            }
        }

        return $articleText;
    }

    /**
     * Remove script tags, form tags, and other elements.
     * @param DOMDocument $doc
     * @return DOMDocument
     */
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

        // Get all child elements
        $childElements = $doc->getElementsByTagName('*');

        // Loop through the elements backwards
        for ($i = $childElements->length - 1; $i >= 0; $i--) {
            $child = $childElements->item($i);

            // Remove elements with specific classes
            foreach (self::$classes_to_remove as $class) {
                if ($child->hasAttribute('class') && $child->getAttribute('class') == $class) {
                    if ($child->parentNode) {
                        $child->parentNode->removeChild($child);
                    }
                    break; // Move to the next child element
                }
            }

            // Remove elements with specific IDs
            foreach (self::$ids_to_remove as $id) {
                if ($child->hasAttribute('id') && $child->getAttribute('id') == $id) {
                    if ($child->parentNode) {
                        $child->parentNode->removeChild($child);
                    }
                    break; // Move to the next child element
                }
            }
        }

        return $doc;
    }

    /**
     * Remove inline styles from the article text.
     * @param DOMDocument $doc
     * @return DOMDocument
     */
    public static function removeInlineStyles($doc)
    {
        $elements = $doc->getElementsByTagName('*');
        foreach ($elements as $element) {
            $element->removeAttribute('style');
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

    /**
     * Extract the article date from the path.
     *
     * @param string $path
     * @return string|null
     */
    public static function extractDateFromString($path)
    {
        // Define the pattern to match the date in the string
        $pattern = '/\/(\d{4})\/(\d{2})\/(\d{2})\//';

        // Perform regex matching to extract the date parts
        preg_match($pattern, $path, $matches);

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

    /**
     * Extract the author from the DOM.
     * @param mixed $doc
     * @return string|void
     */
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

    /**
     * Extract the author's name from the byline.
     *
     * @param string $byline
     * @return string
     */
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

    /**
     * Extract the directory path from a URL.
     *
     * @param string $path
     * @return string
     */
    public static function extractDirPath($path)
    {
        $lastSlashPos = strrpos($path, '/');
        if ($lastSlashPos !== false) {
            $newString = substr($path, 0, $lastSlashPos + 1);
            return $newString; // Output: path/to/
        } else {
            // No forward slash found in the string
            return $path;
        }
    }

    /**
     * Extract the directory path from a URL.
     *
     * @param string $url
     * @return string
     */
    public static function extractFirstDirectory($url)
    {
        $parsedUrl = parse_url($url);

        if (isset($parsedUrl['path'])) {
            $path = $parsedUrl['path'];
            $pathSegments = explode('/', trim($path, '/'));

            if (count($pathSegments) >= 1) {
                return strtolower($pathSegments[0]);
            }
        }

        return null; // Return null if the URL doesn't have a valid path
    }

    /**
     * Create a text-only version of the article, with no HTML, and less text.
     *
     * @param DOMDocument $doc
     * @return DOMDocument
     */
    public static function prepareExtract($doc)
    {
    }

    /**
     * Select the H1 tag in a DOMDocument. Strip HTML tags from the H1, preserving the H1's text content.
     * @param DOMDocument $doc
     * @return DOMDocument
     */
    public static function cleanH1Tag($doc)
    {
        $h1 = $doc->getElementsByTagName('h1')->item(0);
        if ($h1) {
            // Get the text content of the H1 tag
            $h1Text = $h1->textContent;

            // Clear the H1 element
            while ($h1->hasChildNodes()) {
                $h1->removeChild($h1->firstChild);
            }

            // Append the text content into the H1 element
            $h1->appendChild($doc->createTextNode($h1Text));

            return $doc;
        }
        return $doc;
    }
}
