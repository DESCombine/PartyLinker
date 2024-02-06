<?php 
    /**
     * This file is used to send emails
     * It is included in every request that needs to send an email
     * It uses the sendgrid api to send the emails
     * $to -> the email of the receiver
     * $subject -> the subject of the email
     * $notif_text -> the text of the email
     * $name -> the name of the sender
     * $alternative_text -> the alternative text of the email (plain text)
     */
    function sendEmail($to, $subject, $notif_text, $name = "noreply", $alternative_text="") {
        $sender = new \SendGrid\Mail\Mail(); 
        $sender->setFrom("noreply@partylinker.live", $name);
        $sender->setSubject($subject);
        $sender->addTo($to);
        $sender->addContent(
            "text/html", $notif_text
        );
        if ($alternative_text != "") {
            $sender->addContent(
                "text/plain", $alternative_text
            );
        }
        $sendgrid = new \SendGrid(getenv('PL_MAILKEY'));
        try {
            $sendgrid->send($sender);
        } catch (Exception $e) {
            echo 'Caught exception: '. $e->getMessage() ."\n";
        }
    }


?>