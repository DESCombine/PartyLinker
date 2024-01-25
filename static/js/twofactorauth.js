import { request_path } from "/static/js/config.js?v=2";

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

// when screen is resized
window.addEventListener("resize", function(){
    if(window.matchMedia("(min-width: 767px)").matches){
        document.getElementById("extern-container").classList.add("d-flex");
        document.getElementById("extern-container").classList.add("align-middle");
        this.document.getElementById("first").classList.add("align-items-center");
    } else {
        document.getElementById("extern-container").classList.remove("d-flex");
        document.getElementById("extern-container").classList.remove("align-middle");
        this.document.getElementById("first").classList.remove("align-items-center");
    }
});

sendCode();