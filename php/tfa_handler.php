<?php
require_once(getenv("PL_ROOTDIRECTORY") . "php/bootstrap.php");
require_once(getenv("PL_ROOTDIRECTORY") . "db/user.php");

use User\UserUtility;
function tfa_send($username)
{

    global $driver;
    $email = UserUtility::retrieve_email($driver, $username);
    $code = random_int(1111, 9999);
    UserUtility::insert_tfa($driver, $username, $code);
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
    $sender = new \SendGrid\Mail\Mail(); 
    $sender->setFrom("noreply@partylinker.live", "noreply");
    $sender->setSubject($subject);
    $sender->addTo($to);
    $sender->addContent(
        "text/html", $notif_text
    );
    $sendgrid = new \SendGrid(getenv('PL_MAILKEY'));
    try {
        $sendgrid->send($sender);
    } catch (Exception $e) {
        echo 'Caught exception: '. $e->getMessage() ."\n";
    }
}
?>