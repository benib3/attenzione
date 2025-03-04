<?php

namespace App\Validations;

trait UrlValidation
{
  public static function isUrl(string $url)
  {
    $pattern = '/^(https?:\/\/)?'      // Protocol (optional)
      . '(([a-z0-9-]+\.)+[a-z]{2,}'  // Domain name
      . '|localhost)'                 // OR localhost
      . '(\:[0-9]+)?'                 // Port (optional)
      . '(\/[-a-z0-9%_.~+]*)*'        // Path
      . '(\?[;&a-z0-9%_.~+=-]*)?'     // Query string (optional)
      . '(\#[-a-z0-9_]*)?$/i';        // Fragment (optional)

    if (!preg_match($pattern, $url)) {
      throw new \InvalidArgumentException('Invalid URL format');
    }

    // Ensure URL starts with http:// or https://
    if (!preg_match('/^https?:\/\//', $url)) {
      $url = 'https://' . $url;
    }

    return $url;
  }
}
