<?php

namespace Boom\TextFilter\Filter;

use \Embera\Embera;
use DOMDocument;

class OEmbed implements \Boom\TextFilter\Filter
{
    /**
     *
     * @var Embera
     */
    private $embera;

    /**
     *
     * @var DOMDocument
     */
    private $dom;

    public function __construct()
    {
        $this->embera = new Embera();
        $this->dom = new DOMDocument();
    }

    public function filterText($text)
    {
        $this->dom->loadHTML(mb_convert_encoding($text, 'HTML-ENTITIES', 'UTF-8'));

        foreach ($this->dom->getElementsByTagName('p') as $p) {
            $textContent = preg_replace('|\s|', '', $p->textContent);

            if ($p->getElementsByTagName('a')->length == 0 && filter_var($textContent, FILTER_VALIDATE_URL) !== false) {
                $embed = $this->embera->autoEmbed($p->textContent);

                if ($embed !== $p->textContent) {
                    $html = new DOMDocument();
                    $html->loadHTML($embed);

                    foreach ($html->getElementsByTagName('iframe') as $el) {
                        $el = $this->dom->importNode($el);
                        $p->parentNode->replaceChild($el, $p);
                    }
                }
            }
        }

        // See http://php.net/manual/en/domdocument.savehtml.php#85165
        return preg_replace('/^<!DOCTYPE.+?>/', '', str_replace( array('<html>', '</html>', '<body>', '</body>'), array('', '', '', ''), $this->dom->saveHtml()));
    }
}
