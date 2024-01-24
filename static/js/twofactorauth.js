import { request_path } from "/static/js/config.js?v=1";

document.getElementsByTagName("form")[0].action = request_path + "/auth/check_tfa.php";

const urlParams = new URLSearchParams(window.location.search);
const error = urlParams.get('error');

if (error) {
    document.getElementById("error").classList.remove("invisible");
}


async function sendCode() {
    await fetch(request_path + "/email/tfa_handler.php", {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    });
}

sendCode();