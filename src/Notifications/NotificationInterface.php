<?php

namespace App\Notifications;

interface NotificationInterface
{
  public const TITLE = "Attenzione found a tag.";

  public function notify(string $body): void;
}
