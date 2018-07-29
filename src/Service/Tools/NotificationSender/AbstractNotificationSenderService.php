<?php

namespace App\Service\Tools\NotificationSender;

use Doctrine\Common\Collections\Collection;
use Symfony\Component\Templating\EngineInterface;

abstract class AbstractNotificationSenderService
{
	protected $view;
	protected $container;

	public function __construct(EngineInterface $view, array $dependencies = [])
	{
		$this->view = $view;
		$this->container = $dependencies;
	}

	abstract function sendNotifications(Collection $items): void;
}