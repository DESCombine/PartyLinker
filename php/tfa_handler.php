<?php
require_once(getenv("PL_ROOTDIRECTORY") . "php/bootstrap.php");
require_once(getenv("PL_ROOTDIRECTORY") . "db/user.php");
require_once(getenv("PL_ROOTDIRECTORY") . "php/email_utils.php");

use User\UserUtility;
/**
 * This function is used to send the 2FA code to the user
 * @param string $username the username of the user
 */
function tfa_send($username)
{

    global $driver;
    $email = UserUtility::retrieve_email($driver, $username);
    $code = random_int(1111, 9999);
    $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
    $charactersLength = strlen($characters);
    $token = '';
    $tokenLength = 128;
    for ($i = 0; $i < $tokenLength; $i++) {
        $token .= $characters[random_int(0, $charactersLength - 1)];
    }
    UserUtility::insert_tfa($driver, $token, $username, $code);
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

            p {
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
            <h1>Insert this code inside the webpage to login</h1>
        </header>
        <main>
            <div>
                <p>@code</p>
            </div>
        </main>
        <footer>
            <a href='https://partylinker.live'>PartyLinker</a>
        </footer>
    </body>
    
    </html>";
    
    $notif_text = str_replace('@code', $code, $notif_text);
    $to = $email;
    $subject = "2FA Code";
    sendEmail($to, $subject, $notif_text, "PartyLinker", "Insert this code inside the webpage to login: ".$code);
    return $token;
}
?>