<?php

namespace App\Service\Tools\NotificationSender;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Templating\EngineInterface;

class EmailSenderService extends AbstractNotificationSenderService
{
	public function sendNotifications(Collection $items): void
	{
		$emails = $this->getEmailsFromItems($items);
		foreach ($emails as $email => $items) {
			$this->sendEmail($email, $items);
		}
	}

	private function sendEmail(string $email, array $items): void
	{
		$subject = $this->view->render('_notifications/email/subject.html.twig', ['itemsCount' => count($items)]);
		$body = $this->view->render('_notifications/email/body.html.twig', ['items' => $items]);

		$message = (new \Swift_Message($subject))
	        ->setFrom('allefinder.powiadomienia@gmail.com')
	        ->setTo($email)
	        ->setBody($body, 'text/html')
  		;
		
  		$this->container['mailer']->send($message);
	}

	private function getEmailsFromItems(Collection $items): array
	{
		$emails = [];
		foreach ($items as $item) {
			$email = $item->getSearch()->getUser()->getEmail();
			if (!isset($emails[$email])) {
				$emails[$email] = [];
			}

			$emails[$email][] = $item;
		}

		return $emails;
	}
}