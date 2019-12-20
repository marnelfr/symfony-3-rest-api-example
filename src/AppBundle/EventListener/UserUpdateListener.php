<?php
namespace AppBundle\EventListener;

use AppBundle\Events;
use Psr\Log\LoggerInterface;

class UserUpdateListener
{
    private $logger;

    public function __construct(LoggerInterface $logger){
        $this->logger = $logger;
    }

    public function updateLog(Events $events) {
        $this->logger->info($events->getLastname() . ' ' . $events->getFistname() . ' a été modifié');
    }

}
