<?php

namespace App\Message;

final class ContacMessage
{

    public $senderFirstName;
    public $senderLastName;
    public $senderMail;
    public $subject;
    public $messageContent;

    public function __construct($senderFirstName, $senderLastName, $senderMail, $subject, $messageContent)
    {
        $this->senderFirstName = $senderFirstName;
        $this->senderLastName = $senderLastName;
        $this->senderMail = $senderMail;
        $this->subject = $subject;
        $this->messageContent = $messageContent;
    }
}
