<?php

namespace App\Message;

final class SendEmailMessage
{
    /*
     * Add whatever properties & methods you need to hold the
     * data for this message class.
     */

     private $emailTo;
     private $emailFrom;
     private $title;
     private $content;

     public function __construct(string $emailTo, string $emailFrom, string $title, string $content)
     {
         $this->emailTo = $emailTo;
         $this->emailFrom = $emailFrom;
         $this->title = $title;
         $this->content = $content;
     }

    public function getEmailTo(): string
    {
        return $this->emailTo;
    }

    public function getEmailFrom(): string
    {
        return $this->emailFrom;
    }

    public function getTitle(): string
    {
        return $this->title;
    }

    public function getContent(): string
    {
        return $this->content;
    }
}
