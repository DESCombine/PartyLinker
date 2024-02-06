# Authentication
This directory contains the authentication requests for the API.

## auth.php
This file contains the authentication request for the API. It is used to authenticate a user and return a token.
The token won't be returned as a JSON response but it will be set as a cookie.
If 2FA is enabled, the user will be redirected to the 2FA page and no token will be set.
### POST
#### Parameters
- username: The username of the user.
- password: The password of the user.
- remember: A boolean value to remember the user. If false the cookie will be a session cookie.

## logout.php
This file contains the logout request for the API. It is used to logout a user and remove the token from the cookies. 
Users can also provide feedback when they logout.
### POST
#### Parameters
- feedback: The feedback of the user.

## check_tfa.php
This file contains the check TFA request for the API. It is used to validate the TFA code and return a token.
The token won't be returned as a JSON response but it will be set as a cookie.
### POST
#### Parameters
- code: The TFA code of the user.
- token: A temporary token that serves as an authentication session token, it is used to validate the TFA code.

## signin.php
This file contains the signup request for the API. It is used to create a new user.
### POST
#### Parameters
- username: The username of the user.
- password: The password of the user.
- email: The email of the user.
- name: The name of the user.
- surname: The surname of the user.
- birthdate: The birthdate of the user.
