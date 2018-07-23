<?php

namespace App\Service\Tools;

use App\Service\Allegro\AllegroService;
use App\Service\Allegro\AllegroServiceInterface;
use Doctrine\ORM\EntityManagerInterface;

class CategoryUpdateService implements CategoryUpdateServiceInterface
{
	public function __construct(EntityManagerInterface $em, AllegroServiceInterface $allegro)
	{
		$this->em = $em;
		$this->allegro = $allegro;
	}

	public function updateCategories(): void
	{
		$categories = $this->allegro->getCategories();

        $batchSize = 50;
        $i = 0;
        foreach ($categories as $category) {
            $this->em->persist($category);

            $i++;
            if ($i % $batchSize === 0) {
                $this->em->flush();
                $this->em->clear();
            }
        }

        $this->em->flush();
        $this->em->clear();
	}
}
