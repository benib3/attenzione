<?php

namespace App\Scrapper;

/**
 * Interface for web page scraping functionality
 */
interface ScrapperInterface
{
  /**
   * Sets the URL to be scraped
   * 
   * @param string $url The URL of the page to scrape (will be validated)
   * @return static Returns the current instance for method chaining
   */
  public function setUrl(string $url): static;

  /**
   * Sets the HTML tag to search for on the page
   * 
   * @param string $tag The HTML tag selector (e.g., 'button', 'div', etc.)
   * @return static Returns the current instance for method chaining
   */
  public function setTag(string $tag): static;

  /**
   * Sets the tag name or class name to look for within the elements
   * 
   * @param string $tagName The name attribute value or class name to search for
   * @return static Returns the current instance for method chaining
   */
  public function setTagName(string $tagName): static;

  /**
   * Executes the scraping operation
   * 
   * Fetches the page content, searches for elements matching the specified tag,
   * and checks if any of these elements have the specified name attribute or class.
   * 
   * @return bool|string|null 
   *         - true: When the specified tagName is found
   *         - null: When no matching elements are found
   *         - string: Error message in case of exceptions
   */
  public function run(): bool|string|null;
}
