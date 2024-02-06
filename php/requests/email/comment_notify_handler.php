<?php
    require_once(getenv("PL_ROOTDIRECTORY")."php/bootstrap.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/requests/authenticated_request.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/user.php");
    require_once(getenv("PL_ROOTDIRECTORY")."db/post.php");
    require_once(getenv("PL_ROOTDIRECTORY")."php/email_utils.php");
    use User\UserUtility;
    use Post\PostUtility;

    /**  
     * This file is used to send an email to the user when someone comments on their post
     * $commented -> the id of the post that was commented on
     */
    function comment_notify_handler($commented) {
        global $driver;
        global $username;
        $user = PostUtility::from_db_with_post_id($driver, $commented)->getUser();
        $commented_user = UserUtility::from_db_with_username($driver, $user);
        $settings = UserUtility::retrieve_settings($driver, $commented_user->getUsername());
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
                                <h1>Hey @reciever, @sender has left a comment under one of your posts!</h1>
                            </header>
                            <main>
                                <div>
                                    <h2>Check it out at:</h2>
                                    <a href='https://partylinker.live'>PartyLinker</a>
                                </div>
                            </main>
                        </body>
                    </html>";
            $notif_text = str_replace('@reciever', $commented_user->getUsername(), $notif_text);
            $notif_text = str_replace('@sender', $username, $notif_text);
            $to = $commented_user->getEmail();
            $subject = "You have a new comment!";
            sendEmail($to, $subject, $notif_text, "PartyLinker", "Hey ".$commented_user->getUsername().", you have a new comment by ".$username."! Check it out at https://partylinker.live");
        }
    }
?>