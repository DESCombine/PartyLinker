import { request_path } from "/static/js/config.js";

async function loadEvent(event_id) {
    const response = await fetch(request_path + "/user/load_event.php?event=" + event_id);
    const event = await response.json();
    return event;
}

async function loadUserImage(user_id) {
    const response = await fetch(request_path + "/user/load_user_img.php?user=" + user_id);
    const image = await response.json();
    return image;
}

function addNewPost(feed, post_id, user_photo, name, image, description, event = null) {
    let template = document.getElementById("post-template");
    let clone = document.importNode(template.content, true);
    clone.querySelector("#post-user-photo").src = user_photo;
    clone.querySelector("#post-name").innerHTML = name;
    clone.querySelector("#post-photo").src = image;
    clone.querySelector("#comments-button").onclick = "showComments(" + post_id + ")";
    clone.querySelector("#partecipations-button").onclick = "showPartecipations(" + post_id + ")";
    clone.querySelector("#post-description").innerHTML = description;
    if (event) {
        addEventDescription(clone, event);
    }
    feed.appendChild(clone);
}

function addEventDescription(post, event) {
    let template = document.getElementById("description-template");
    let clone = document.importNode(template.content, true);
    clone.querySelector("#event-place").innerHTML = event.place;
    clone.querySelector("#event-date").innerHTML = event.date;
    clone.querySelector("#event-times").innerHTML = event.times;
    clone.querySelector("#event-vips").innerHTML = event.vips;
    clone.querySelector("#event-people").innerHTML = event.max_people;
    clone.querySelector("#event-price").innerHTML = event.price;
    clone.querySelector("#event-age").innerHTML = event.min_age;
    post.appendChild(clone);
}

async function loadPhotoComments(photo_id) {
    const response = await fetch(request_path + "/user/load_comments_photo.php?photo=" + photo_id);
    const comments = await response.json();
    return comments;
}

async function loadEventComments(event_id) {
    const response = await fetch(request_path + "/user/load_comments_event.php?event=" + event_id);
    const comments = await response.json();
    return comments;
}

async function showComments(id_post) {
    const comments = document.getElementById("comments");
    const photo_comments = await loadPhotoComments(id_post);
    const event_comments = await loadEventComments(id_post);
    if (photo_comments.length == 0) {
        comments_to_show = event_comments;
    } else {
        comments_to_show = photo_comments;
    }
    let template = document.getElementById("comment-template");
    let clone = document.importNode(template.content, true);
    for (let i = 0; i < comments_to_show.length; i++) {
        let comment = comments_to_show[i];
        clone.querySelector("#comment-user-photo").src = await loadUserImage(comment.poster);
        clone.querySelector("#comment-name").textContent = comment.poster;
        clone.querySelector("#comment-content").textContent = comment.content;
        comments.appendChild(clone);
    }
}

async function loadPartecipations(event_id) {
    const response = await fetch(request_path + "/user/load_partecipations.php?event=" + event_id);
    const partecipations = await response.json();
    return partecipations;
}

async function showPartecipations(event_id) {
    const partecipations = document.getElementById("partecipations");
    const partecipations_list = await loadPartecipations(event_id);
    let template = document.getElementById("partecipation-template");
    let clone = document.importNode(template.content, true);
    for (let i = 0; i < partecipations_list.length; i++) {
        let partecipation = partecipations_list[i];
        clone.querySelector("#partecipation-user-photo").src = await loadUserImage(partecipation.partecipant);
        clone.querySelector("#partecipation-name").textContent = partecipation.partecipant;
        partecipations.appendChild(clone);
    }
}

async function likePost(post_id) {
    const response = await fetch(request_path + "/user/like_post.php?post=" + post_id, {
        method: "GET",
    });
    const post = document.getElementsByName(post_id)[0];
    const likes = post.querySelector("#post-likes");
    likes.innerHTML = parseInt(likes.innerHTML) + 1;
}