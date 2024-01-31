<?php 
    use Firebase\JWT\JWT;
    function generate_token($username) {
        $key = getenv("PL_JWTKEY");
        $payload = array(
            "username" => $username,
        );
        $jwt = JWT::encode($payload, $key, 'HS256');
        return $jwt;
    }

    function set_token_cookie($username, $remember = "off") {
        $cookie_name = "token";
        $cookie_value = "Bearer ".generate_token($username);
        $cookie_options = array(
            'path' => '/',
            'secure' => true,
            'httponly' => true,
            'samesite' => 'None',
        );
        if($_SERVER["HTTP_HOST"] != "localhost") {
            $cookie_options['domain'] = '.partylinker.live';
        } 
        if($remember == "on") {
            $cookie_options['expires'] = time() + 86400 * 365;
        }
        setcookie($cookie_name, $cookie_value, $cookie_options);
    }

?>