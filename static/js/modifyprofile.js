import { request_path } from "/static/js/config.js?v=2";

document.getElementsByTagName("form")[0].action = request_path + "/user/modifyprofile.php";

if (window.matchMedia("(min-width: 767px)").matches) {
    document.getElementsByTagName('img')[0].classList.add('d-none')
}

window.addEventListener("resize", function(){
    if(window.matchMedia("(min-width: 767px)").matches){
        document.getElementsByTagName('img')[0].classList.add('d-none')
    } else {
        document.getElementsByTagName('img')[0].classList.remove('d-none')
    }
});