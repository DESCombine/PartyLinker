<?php
require_once(getenv("PL_ROOTDIRECTORY") . "php/bootstrap.php");
require_once(getenv("PL_ROOTDIRECTORY") . "php/requests/authenticated_request.php");
require_once(getenv("PL_ROOTDIRECTORY") . "db/user.php");
use User\UserUtility;

function tfa_send()
{
    global $driver;
    global $username;
    $email = UserUtility::retrieve_email($driver, $username);
    $code = random_int(1111, 9999);
    UserUtility::insert_tfa($driver, $username, $code);
    $notif_text = 
    "<html>
        <body>
            <header>
                <h1>Insert this code inside the webpage to login</h1>
            </header>
            <main>
                <p>@code</p>
            </main>
            <footer>
                <a href='https://partylinker.live'>PartyLinker</a>
            </footer>
        </body>
    </html>";
    
    $notif_text = str_replace('@code', $code, $notif_text);
    $to = $email;
    $subject = "2FA Code";
    $headers = "From: PartyLinker <noreply@partylinker.com>";
    mail($to, $subject, $notif_text, $headers);
}

tfa_send();
?>