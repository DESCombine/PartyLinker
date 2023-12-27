# Requests
This directory contains the requests for the API.
In case of an error the API will return a JSON object with the following structure:
```json
{
    "error": "error_message"
}
```


## auth
This directory contains the authentication requests for the API.
- [auth.php](auth/auth.php)
- [token.php](auth/token.php)

## user
This directory contains the user requests for the API.
- [registration.php](user/registration.php)
- [search_user.php](user/search_user.php)