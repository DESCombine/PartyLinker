import { request_path } from "/static/js/config.js?v=1";

const event_id = new URLSearchParams(window.location.search).get('event');

if (event_id == 0) {
    document.getElementsByTagName("h1")[0].innerHTML = "Upload new event";
    document.querySelector("#event-inputs").classList.remove("invisible");
} else {
    document.getElementsByTagName("h1")[0].innerHTML = "Upload post for<br> event " + event_id;
    document.querySelector("form div").classList.add("overflow-y-hidden");
}

document.getElementsByName("event_id")[0].value = event_id;
document.getElementsByTagName("form")[0].action = request_path + "/upload_post.php";