<?php

namespace App\Support\Seo;

use App\Support\Localization\Locale;
use DateTimeInterface;
use XMLWriter;

/**
 * Builds an XML sitemap in which every URL lists its translations as
 * xhtml:link hreflang alternates (including x-default).
 */
final class SitemapBuilder
{
    /**
     * @var list<array{alternates: array<string, string>, lastModified: ?DateTimeInterface}>
     */
    private array $entries = [];

    /**
     * @param  array<string, string>  $alternates  Locale code => absolute URL.
     */
    public function add(array $alternates, ?DateTimeInterface $lastModified = null): self
    {
        $this->entries[] = ['alternates' => $alternates, 'lastModified' => $lastModified];

        return $this;
    }

    public function count(): int
    {
        return array_sum(array_map(fn (array $entry): int => count($entry['alternates']), $this->entries));
    }

    public function toXml(): string
    {
        $xml = new XMLWriter;
        $xml->openMemory();
        $xml->setIndent(true);
        $xml->startDocument('1.0', 'UTF-8');

        $xml->startElement('urlset');
        $xml->writeAttribute('xmlns', 'http://www.sitemaps.org/schemas/sitemap/0.9');
        $xml->writeAttribute('xmlns:xhtml', 'http://www.w3.org/1999/xhtml');

        foreach ($this->entries as $entry) {
            foreach ($entry['alternates'] as $url) {
                $xml->startElement('url');
                $xml->writeElement('loc', $url);

                if ($entry['lastModified'] !== null) {
                    $xml->writeElement('lastmod', $entry['lastModified']->format(DateTimeInterface::ATOM));
                }

                foreach ($entry['alternates'] as $locale => $alternateUrl) {
                    $this->writeAlternate($xml, $locale, $alternateUrl);
                }

                if (isset($entry['alternates'][Locale::fallback()->value])) {
                    $this->writeAlternate($xml, 'x-default', $entry['alternates'][Locale::fallback()->value]);
                }

                $xml->endElement();
            }
        }

        $xml->endElement();
        $xml->endDocument();

        return $xml->outputMemory();
    }

    private function writeAlternate(XMLWriter $xml, string $hreflang, string $url): void
    {
        $xml->startElement('xhtml:link');
        $xml->writeAttribute('rel', 'alternate');
        $xml->writeAttribute('hreflang', $hreflang);
        $xml->writeAttribute('href', $url);
        $xml->endElement();
    }
}
