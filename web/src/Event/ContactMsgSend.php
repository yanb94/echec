<?php

namespace App\Event;

class ContactMsgSend
{
    public function __construct(
        private string $firtsname,
        private string $lastname,
        private string $email,
        private string $message,
    ) {
    }
    
    /**
     * Get the value of firtsname
     */
    public function getFirtsname():string
    {
        return $this->firtsname;
    }

    /**
     * Get the value of lastname
     */
    public function getLastname():string
    {
        return $this->lastname;
    }

    /**
     * Get the value of email
     */
    public function getEmail():string
    {
        return $this->email;
    }

    /**
     * Get the value of message
     */
    public function getMessage():string
    {
        return $this->message;
    }
}
