import { request_path } from "/static/js/config.js?v=1";

async function tryLogin() {
    username = document.getElementById("username").value
    password = document.getElementById("password").value
    //fetch(request_path + "/auth/auth.php", {
    const response = await fetch(request_path + "/auth/auth.php", {
        method: "POST",
        credentials: "include",
        header: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            "username": username,
            "password": password
        })
    })
    const data = await response.json()
    if(!data.hasOwnProperty("error")) {
        window.location.href = "/"
    } else {
        //TODO: Show error message
    }
}