function tryLogin() {
    username = document.getElementById("username")
    password = document.getElementById("password")
    //fetch("https://api.partylinker.live/auth/auth.php", {
    fetch("http://localhost/php/requests/auth/auth.php", {
        method: "POST",
        body: {
            "username": username,
            "password": password
        }
    })
}