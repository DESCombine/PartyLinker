<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    use User\UserUtility;
 
    function event_notify_handler() {
        global $driver;
        global $username;
        $followers = UserUtility::retrieve_followers($driver, $username);
        foreach ($followers as $follower) {
            $settings = UserUtility::retrieve_settings($driver, $follower->getUsername());
            if ($settings->getNotifications()) {
                $notif_text = "Hey @reciever, @sender has published a new event! Check it out at partylinker.live";
                $notif_text = str_replace('@reciever', $follower->getUsername(), $notif_text);
                $notif_text = str_replace('@sender', $username, $notif_text);
                $to = $follower->getEmail();
                $subject = "A new event is up!";
                $headers = "From: PartyLinker <noreply@partylinker.com>";
                mail($to, $subject, $notif_text, $headers);
            }
        }
    }
?>