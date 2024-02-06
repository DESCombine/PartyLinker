<?php 
    /**
     * This file is used to send emails
     * It is included in every request that needs to send an email
     * It uses the sendgrid api to send the emails
     * @param string $to the email address of the recipient
     * @param string $subject the subject of the email
     * @param string $notif_text the html content of the email
     * @param string $name the name of the sender
     * @param string $alternative_text the plain text content of the email
     * @return void
     * @throws Exception
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