<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\Tools\SearchUpdateService;
use App\Service\Tools\SearchUpdateServiceInterface;

class UpdateSearchesCommand extends Command
{
	private $em;

	public function __construct(SearchUpdateServiceInterface $searchUpdateService)
    {
        $this->searchUpdateService = $searchUpdateService;

        parent::__construct(); 
    }

    protected function configure()
    {
        $this
	        ->setName('app:update-searches')
	        ->setDescription('Updates searches.')
	        //->setHelp('...')
	    ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->searchUpdateService->updateSearches(
            $this->searchUpdateService->getActiveSearches()
        );
    }
}
