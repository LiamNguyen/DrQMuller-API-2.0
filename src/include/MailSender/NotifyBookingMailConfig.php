<?php
require 'Values.php';
require dirname(__FILE__) . '/../../../lib/PHPMailer/PHPMailerAutoload.php';

class NotifyBookingMailConfig {

    private $mailSender;

    function __construct()
    {
        $this->mailSender = new PHPMailer();

        $this->mailSender->isSMTP();
        $this->mailSender->Host = host;
        $this->mailSender->SMTPAuth = true;
        $this->mailSender->Username = username;
        $this->mailSender->Password = password;
        $this->mailSender->SMTPSecure = smtpSecure;
        $this->mailSender->Port = port;
        $this->mailSender->CharSet = charSet;
        $this->mailSender->isHTML(true);
        $this->mailSender->AltBody = altBody;
        $this->mailSender->Subject = notifyBooking_EmailSubject;

        $this->mailSender->setFrom(sendFromEmail, sendFromName,true);
        $this->mailSender->addAddress(notifyBooking_SendToEmail, notifyBooking_SendToName);
    }

    public function getMailSender() {
        return $this->mailSender;
    }
}
