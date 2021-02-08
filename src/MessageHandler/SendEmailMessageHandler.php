<?php

namespace App\MessageHandler;

use App\Message\SendEmailMessage;
use Symfony\Component\Messenger\Handler\MessageHandlerInterface;

final class SendEmailMessageHandler implements MessageHandlerInterface
{
    private \Swift_Mailer $mailer;

    public function __construct(\Swift_Mailer $mailer)
    {
        $this->mailer = $mailer;
    }

    public function __invoke(SendEmailMessage $message)
    {
        $msg = (new \Swift_Message($message->getTitle()))
            ->setFrom($message->getEmailFrom())
            ->setTo($message->getEmailTo())
            ->setBody($message->getContent(), 'text/html')
            ->addPart($message->getContent(), 'text/plain')
        ;

        $this->mailer->send($msg);
    }
}
