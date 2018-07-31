<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\Tools\SearchUpdateService;
use App\Service\Tools\SearchUpdateServiceInterface;
use App\Service\Tools\NotificationsService;
use App\Service\Tools\NotificationsServiceInterface;

class UpdateSearchesCommand extends Command
{
	private $searchUpdateService;
    private $notificationsService;

	public function __construct(SearchUpdateServiceInterface $searchUpdateService, NotificationsServiceInterface $notificationsService)
    {
        $this->searchUpdateService = $searchUpdateService;
        $this->notificationsService = $notificationsService;

        parent::__construct(); 
    }

    protected function configure()
    {
        $this
	        ->setName('app:update-searches')
	        ->setDescription('Updates searches and sends notifications of new items.')
	        //->setHelp('...')
	    ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->searchUpdateService->updateSearches(
            $this->searchUpdateService->getActiveSearches()
        );

        $this->notificationsService->sendNotifications();
    }
}
