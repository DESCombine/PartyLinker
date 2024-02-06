import { request_path } from "/static/js/config.js?v=10";

// loads form action
document.getElementsByTagName("form")[0].action = request_path + "/auth/check_tfa.php";

// check url for error
const urlParams = new URLSearchParams(window.location.search);
const error = urlParams.get('error');
const token = urlParams.get('token');
document.getElementById("tokenInput").setAttribute("value", token);
document.getElementById("rememberInput").setAttribute("value", urlParams.get('remember'));
if (error) {
    document.getElementById("error").classList.remove("invisible");
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
