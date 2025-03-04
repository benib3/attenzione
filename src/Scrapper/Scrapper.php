<?php

namespace App\Scrapper;

use App\Validations\Validation;
use Symfony\Contracts\HttpClient\HttpClientInterface;
use Symfony\Component\DomCrawler\Crawler;

class Scrapper extends Validation implements ScrapperInterface
{
  private string $url;
  private string $tag;
  private string $tagName;

  public function __construct(
    private HttpClientInterface $http,
  ) {}

  public function setUrl(string $url): static
  {
    $this->url = $this->isUrl($url);

    return $this;
  }

  public function setTag(string $tag): static
  {
    $this->tag = $tag;

    return $this;
  }

  public function setTagName(string $tagName): static
  {
    $this->tagName = $tagName;

    return $this;
  }

  public function run(): bool|string|null
  {
    try {
      // Fetch and parse the page content
      $response = $this->http->request('GET', $this->url);
      $content = $response->getContent();
      $crawler = new Crawler($content);

      // First try to find by tag name attribute
      $attributes = $this->findTag($crawler);

      // If nothing found, try by class
      if (empty($attributes)) {
        $attributes = $this->findClass($crawler);
      }

      // If still nothing found, return null
      if (empty($attributes)) {
        return null;
      }

      // Check if the tag name we're looking for exists in found attributes
      return in_array($this->tagName, $attributes, true) ? true : null;
    } catch (\Exception $e) {
      return $e->getMessage();
    }
  }

  private function findTag(Crawler $crawler): array
  {
    try {
      $elements = $crawler->filter($this->tag)->each(function (Crawler $attr) {
        return $attr->attr('name');
      });
      $attrs = array_filter($elements);

      if (!empty($attrs)) {
        return $attrs;
      }

      return [];
    } catch (\InvalidArgumentException $e) {
      // Original selector was invalid
      return [];
    }
  }

  private function findClass(Crawler $crawler): array
  {
    $classSelector = $this->tag . '.' . $this->tagName;

    try {
      $classElements = $crawler->filter($classSelector);

      if ($classElements->count() > 0) {
        return [$this->tagName];
      }

      return [];
    } catch (\InvalidArgumentException $e) {
      return [];
    }
  }
}
