import { request_path } from "/static/js/config.js?v=14";

// loads form action
document.getElementsByTagName("form")[0].action = request_path + "/auth/signin.php";

// when screen is resized
window.addEventListener("resize", function () {
    if (window.matchMedia("(min-width: 767px)").matches) {
        document.getElementById("extern-container").classList.add("d-flex");
        document.getElementById("extern-container").classList.add("align-middle");
        this.document.getElementById("first").classList.add("align-items-center");
    } else {
        document.getElementById("extern-container").classList.remove("d-flex");
        document.getElementById("extern-container").classList.remove("align-middle");
        this.document.getElementById("first").classList.remove("align-items-center");
    }
});

// check url for error
if(window.location.href.indexOf("?wrongusername") > -1){
    document.getElementById("error").classList.remove("invisible");
}