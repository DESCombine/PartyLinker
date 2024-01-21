<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    use User\UserUtility;

    function tag_notify_handler($text) {
        global $driver;
        global $username;

        $tag_start = strpos($text, '@');
        if ($tag_start) {
            $tag_end = strpos($text, ' ', $tag_start);
            if($tag_end === false) {
                $tag_end = strlen($text);
            }
            $tag = substr($text, $tag_start + 1, $tag_end - $tag_start - 1);
            $tagged_user = UserUtility::from_db_with_username($driver, $tag);
            if ($tagged_user) {
                $notif_text = "Hey @reciever, you have been tagged in a post by @sender!\n
                        Check it out at partylinker.live";
                $notif_text = str_replace('@reciever', $tagged_user->getUsername(), $notif_text);
                $notif_text = str_replace('@sender', $username, $notif_text);
                $to = $tagged_user->getEmail();
                $subject = "You have been tagged in a post!";
                $headers = "From: PartyLinker <noreply@partylinker.com>";
                //mail($to, $subject, $notif_text, $headers);
            }
        }
    }
?>