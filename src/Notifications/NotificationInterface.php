<?php

namespace App\Notifications;

interface NotificationInterface
{
  public function notify(string $body): void;
}
