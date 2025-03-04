<?php

namespace App\Notifications;

interface NotificationInterface
{
  private const TITLE = "Attenzione found a tag.";

  public function notify(string $body): void;
}
