<?php

namespace App\Notifications;

use Joli\JoliNotif\Notification;
use Joli\JoliNotif\DefaultNotifier;


class Notifications implements NotificationInterface
{
  private const TITLE = "Attenzione found a tag.";

  public function notify(string $body): void
  {
    $notifier = new DefaultNotifier();

    $notification =
      (new Notification())
      ->setTitle(self::TITLE)
      ->setBody($body)
      // ->setIcon(__DIR__ . '/path/to/your/icon.png') //TODO: Add icon :)
    ;

    $notifier->send($notification);
  }
}
