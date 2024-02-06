import { request_path } from "/static/js/config.js?v=9";

// adds the action to the form
document.getElementsByTagName("form")[0].action = request_path + "/auth/auth.php";

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

// check url for error
if(window.location.href.indexOf("?wrongpassword") > -1){
    document.getElementById("error").classList.remove("invisible");
    document.getElementById("error").innerHTML = "Error! Invalid password";
}
if (window.location.href.indexOf("?usernotfound") > -1){
    document.getElementById("error").classList.remove("invisible");
    document.getElementById("error").innerHTML = "Error! Invalid username";
}