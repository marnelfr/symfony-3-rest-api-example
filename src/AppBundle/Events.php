<?php

namespace AppBundle;

use AppBundle\Entity\User;
use Symfony\Component\EventDispatcher\Event;

class Events extends Event {
    // Prospect event
    const USER_UPDATED = 'user.updated';

    private $firstname;
    private $lastname;

    public function __construct(User $user){
        $this->firstname = $user->getFirstname();
        $this->lastname = $user->getLastname();
    }

    public function getFistname() {
        return $this->firstname;
    }

    public function getLastname() {
        return $this->lastname;
    }

}