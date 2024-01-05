# Authentication
This directory contains the authentication requests for the API.

## auth.php
This file contains the authentication request for the API. It is used to authenticate a user and return a token.
The token won't be returned as a JSON response but it will be set as a cookie.
### POST
Sample request:
```json
{
    "username": "username",
    "password": "password"
}
```

Sample response:
```json
{
    "message": "success",
}
```
## token.php
### GET
This file contains the token request for the API. It is used to validate a token and return the info of the user.

The request must contain the token in the header as follows:
```
Authorization: Bearer jwt_token
```

Sample response:
```json
{
    "username": "username",
    "email": "email",
    "name": "name",
    "surname": "surname",
    "birth_date": "birth_date",
    "photo": "photo",
    "bio": "bio",
    "phone": "phone",
}
```