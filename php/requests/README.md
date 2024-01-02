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

# Authenticated Requests and Cors

## Authenticated Requests
All authenticated requests require a valid token. The token is sent in the header of the request.

By requiring the [authenticated_request.php](authenticated_requests.php) file in the request file, the token will be checked.

If the token is valid, the request will be executed. If the token is invalid, the request will return an error.

## Cors
Since setting the header for every request is a lot of work, the [cors.php](cors.php) file is required in every request file.