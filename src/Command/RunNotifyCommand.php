<?php

namespace App\Command;

use App\Notifications\NotificationInterface;
use App\Scrapper\ScrapperInterface;
use Carbon\Carbon;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
  name: 'app:run-notify',
  description: 'Runs a web scrapper for finding a tag with name if its present then it notifies via console if its there',
)]
class RunNotifyCommand extends Command
{
  public function __construct(
    private ScrapperInterface $scrapper,
    private NotificationInterface $notification
  ) {
    parent::__construct();
  }

  protected function configure(): void
  {
    $this
      ->addArgument('url', InputArgument::REQUIRED, 'Url of page that needs to be checked')
      ->addArgument('tag', InputArgument::REQUIRED, 'Tag that needs to be checked for visibility')
      ->addArgument('tagName', InputArgument::REQUIRED, 'Html tml tag name or class name  needs to be checked for visibility')
    ;
  }

  protected function execute(InputInterface $input, OutputInterface $output): int
  {
    $io = new SymfonyStyle($input, $output);
    $url = $input->getArgument('url');
    $tag = $input->getArgument('tag');
    $tagName = $input->getArgument('tagName');

    $io->info('Running...');

    $result = $this->scrapper
      ->setUrl($url)
      ->setTag($tag)
      ->setTagName($tagName)
      ->run();

    $io->info('Scrapped processing...');

    if (!$result) {
      $io->info(Carbon::now() . " - Nothing for " . $tagName . "|" . "$url");
    } else {
      $output->writeln("<info>$tagName" . "|" . "$url </info>");
      $this->notification->notify("Found tag \"$tagName\" on $url");
    }

    return Command::FAILURE;
  }
}
