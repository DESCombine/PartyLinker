<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/email_utils.php");
    use User\UserUtility;

    function follow_notify_handler($followed) {
        global $driver;
        global $username;
        $followed_user = UserUtility::from_db_with_username($driver, $followed);
        $settings = UserUtility::retrieve_settings($driver, $followed_user->getUsername());
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
                                    color: #fff;
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
                                <h1>Hey @reciever, @sender is now following you!</h1>
                            </header>
                            <main>
                                <div>
                                    <h2>Check it out at:</h2>
                                    <a href='https://partylinker.live'>PartyLinker</a>
                                </div>
                            </main>
                        </body>
                    </html>";
            $notif_text = str_replace('@reciever', $followed_user->getUsername(), $notif_text);
            $notif_text = str_replace('@sender', $username, $notif_text);
            $to = $followed_user->getEmail();
            $subject = "You have a new follower!";
            sendEmail($to, $subject, $notif_text, "PartyLinker", "Hey ".$followed_user->getUsername().", you have a new follower ".$username."! Check it out at https://partylinker.live");
        }
    }
?>