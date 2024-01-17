import { request_path } from "/static/js/config.js";
import { loadEvent, heartPost, showComments } from "/static/js/utils.js";

async function loadPoster() {
    const response = await fetch(request_path + "/user/load_event_poster.php?event=" + "12345");
    const poster = await response.json();
    return poster;
}

async function showContent() {
    // poster
    let post = await loadPoster();
    console.log(post);
    document.getElementById("photo-id").src = "/static/img/uploads/" + post.image;
    document.getElementById("hearts-button").addEventListener("click", function () { heartPost(post_id); });
    document.getElementById("comments-button").addEventListener("click", function () { showComments(post_id); })
    let event = await loadEvent(12345);
    console.log(event);
    document.getElementById("event-name").innerHTML = event.name;
    document.getElementById("event-day").innerHTML = event.starting_date.split(" ")[0];
    document.getElementById("event-time").innerHTML = event.starting_date.split(" ")[1];
    document.getElementById("event-vip").innerHTML = event.vip;
    document.getElementById("event-maxpeople").innerHTML = event.max_capacity;
    document.getElementById("event-price").innerHTML = event.price;
    document.getElementById("event-min-age").innerHTML = event.minimum_age;
    document.getElementById("event-description").innerHTML = post.description;
    // photos
    //showPhotos();
    // partecipants
    //showPeople();
}

showContent();