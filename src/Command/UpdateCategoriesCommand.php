<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Service\Tools\CategoryUpdateService;
use App\Service\Tools\CategoryUpdateServiceInterface;

class UpdateCategoriesCommand extends Command
{
	private $em;

	public function __construct(CategoryUpdateServiceInterface $categoryUpdateService)
    {
        $this->categoryUpdateService = $categoryUpdateService;

        parent::__construct(); 
    }

    protected function configure()
    {
        $this
	        ->setName('app:update-categories')
	        ->setDescription('Updates locally stored Allegro categories.')
	        //->setHelp('...')
	    ;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $this->categoryUpdateService->updateCategories();
    }
}
