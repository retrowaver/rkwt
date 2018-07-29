<?php

namespace App\Service\Tools;

use App\Repository\ItemRepository;
use App\Service\Tools\NotificationSender\EmailSenderService;
use Symfony\Component\Templating\EngineInterface;
use Doctrine\ORM\EntityManagerInterface;

class NotificationsService implements NotificationsServiceInterface
{
	private $itemRepository;
	private $em;
	private $senders;

	public function __construct(ItemRepository $itemRepository, EntityManagerInterface $em, EngineInterface $view, \Swift_Mailer $mailer)
	{
		$this->itemRepository = $itemRepository;
		$this->em = $em;
		$this->senders = [
			new EmailSenderService($view, ['mailer' => $mailer])
		];
	}

	public function sendNotifications(): void
	{
		// Get items
		$items = $this->itemRepository->findByStatus(2);

		// Send notifications
		foreach ($this->senders as $sender) {
			$sender->sendNotifications($items);
		}

		// Set items' status ....................................
		foreach ($items as $item) {
			$item->setStatus(1);
		}

		$this->em->flush();
	}
}
