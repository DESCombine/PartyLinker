<?php 
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