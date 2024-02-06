<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/email_utils.php");
    use User\UserUtility;
    
    /**
     * This file is used to send an email to the user when someone you follow posts a new event
     */
    function event_notify_handler() {
        global $driver;
        global $username;
        $followers = UserUtility::retrieve_followers($driver, $username);
        foreach ($followers as $follower) {
            $settings = UserUtility::retrieve_settings($driver, $follower->getUsername());
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
                                        color: #fff;
                                        padding: 2rem;
                                        border-radius: 1rem;
                                        display: inline-block;
                                    }

                                    h2, a {
                                        color: #fff;
                                        font-size: 2rem;
                                    }

                                    h1 {
                                        color: #000;
                                        font-size: 2.5rem;
                                    }
                                </style>
                            </head>
                            <body>
                                <header>
                                    <h1>Hey @reciever, @poster has published a new event!</h1>
                                </header>
                                <main>
                                    <div>
                                        <h2>Check it out at:</h2>
                                        <a href='https://partylinker.live'>PartyLinker</a>
                                    </div>
                                </main>
                            </body>
                        </html>";
                $notif_text = str_replace('@reciever', $follower->getUsername(), $notif_text);
                $notif_text = str_replace('@poster', $username, $notif_text);
                $to = $follower->getEmail();
                $subject = "A new event is up!";
                $headers = "From: PartyLinker <noreply@partylinker.com>";
                sendEmail($to, $subject, $notif_text, "PartyLinker", "Hey ".$follower->getUsername().", ".$username." has published a new event! Check it out at https://partylinker.live");
            }
        }
    }
?>