<?php

namespace AppBundle\Services;

class Contact
{
    private $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function sendEmail($recipient, $sender, $senderName, $object, $message, $projectTitle)
    {
        $message = \Swift_Message::newInstance()
            ->setSubject($object)
            ->setFrom($sender, $senderName)
            ->setTo($recipient)
            ->setBody("[You've received this email from ".$projectTitle."]
            ".$message, 'text/plain')
        ;
        $this->mailer->send($message);
    }
}