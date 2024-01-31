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
                    $notif_text =
                            "<html>
                                <head>
                                    <style>
                                        body {
                                            background-color: #fff;
                                            color: #000;
                                            font-family: Arial, Helvetica, sans-serif;
                                            font-size: 1.5rem;
                                            text-align: center;
                                        }
                                
                                        header {
                                            padding: 2rem;
                                        }
                                
                                        main {
                                            padding: 2rem;
                                        }
                                
                                        footer {
                                            padding: 2rem;
                                        }
                                
                                        a {
                                            color: #000;
                                            text-decoration: none;
                                        }
                                
                                        div {
                                            background-color: #000;
                                            color: #000;
                                            padding: 2rem;
                                            border-radius: 1rem;
                                            display: inline-block;
                                        }

                                        h2, a {
                                            color: #fff;
                                            font-size: 2rem;
                                        }
                                    </style>
                                </head>
                                <body>
                                    <header>
                                        <h1>Hey @reciever, you have been tagged in a post by @sender</h1>
                                    </header>
                                    <main>
                                        <div>
                                            <h2>Check it out at:</h2>
                                            <a href='https://partylinker.live!'>PartyLinker</a>
                                        </div>
                                    </main>
                                </body>
                            </html>";
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