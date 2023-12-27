# User
This directory contains the user requests for the API.

## registration.php
This file contains the registration request for the API. It is used to register a new user.
### POST
Sample request:
```json
{
    "username": "username",
    "password": "password",
    "email": "email",
    "name": "name",
    "surname": "surname",
    "birth_date": "birth_date",
    "photo": "photo",
    "bio": "bio",
    "phone": "phone",
}
```

Sample response:
```json
{
    "message": "User registered successfully."
}
```

## search_user.php
This file contains the search user request for the API. It is used to search users by their username. It returns an array of users
### GET
The request will contain the username in the URL as follows:
```
url?query=username
```

Sample response:
```json
{
    "users": [
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
    ]
}
```