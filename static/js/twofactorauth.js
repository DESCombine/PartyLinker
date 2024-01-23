import { request_path } from "/static/js/config.js?v=1";
// check if error is present in GET
const urlParams = new URLSearchParams(window.location.search);
const error = urlParams.get('error');

if (error) {
    document.getElementById("error").classList.remove("invisible");
}


async function sendCode() {
    const response = await fetch(request_path + "/email/tfa_handler.php", {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    });
}

sendCode();