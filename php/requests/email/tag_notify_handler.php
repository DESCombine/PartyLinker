<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    use User\UserUtility;

    function tag_notify_handler($text) {
        global $driver;
        global $username;
        $tags = getTags($text);
        foreach ($tags as $tag) {
            $tagged_user = UserUtility::from_db_with_username($driver, $tag);
            if ($tagged_user) {
                $settings = UserUtility::retrieve_settings($driver, $tagged_user->getUsername());
                if ($settings->getNotifications()) {
                    $notif_text = "Hey @reciever, you have been tagged in a post by @sender! Check it out at partylinker.live";
                    $notif_text = str_replace('@reciever', $tagged_user->getUsername(), $notif_text);
                    $notif_text = str_replace('@sender', $username, $notif_text);
                    $to = $tagged_user->getEmail();
                    $subject = "You have been tagged in a post!";
                    $headers = "From: PartyLinker <noreply@partylinker.com>";
                    mail($to, $subject, $notif_text, $headers);
                }
            }
        }
    }

    function getTags($text) {
        $words = explode(" ", $text);
        $tags = array();
        foreach ($words as $word) {
            if (strpos($word, '@') === 0) {
                array_push($tags, substr($word, 1));
            }
        }
        return $tags;
    }
?>