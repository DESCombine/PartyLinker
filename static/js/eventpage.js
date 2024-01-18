import { request_path } from "/static/js/config.js";
import { loadEvent, showComments, loadPartecipations, showPartecipations, addlike, removelike, loadUserImage } from "/static/js/utils.js";

async function loadPoster() {
    const response = await fetch(request_path + "/user/load_event_poster.php?event=" + "12345");
    const poster = await response.json();
    return poster;
}

async function loadPhotos() {
    const response = await fetch(request_path + "/user/load_event_posts.php?event=" + "12345");
    const photos = await response.json();
    return photos;
}

function openModal(post) {
    console.log(post);
    const modal = document.getElementById("post-modal");
    showModalPost(modal, post.id, post.event_id, post.user_photo, post.username, post.image, post.description, post.likes, post.event_post);
}

async function showModalPost(modal, post_id, event_id, user_photo, username,
    image, description, likes, event) {
    document.getElementById("post-user-photo").src = "/static/img/uploads/" + await loadUserImage(username);
    document.getElementById("post-name").innerHTML = username;
    document.getElementById("post-photo").src = "/static/img/uploads/" + image;
    const likeButton =  document.getElementById("likes-button-modal");
    if (post.liked) {
        likeButton.addEventListener("click", function() { removelike(post_id, 'post'); });
        likeButton.innerHTML = "&#10084";
    } else {
        likeButton.addEventListener("click", function() { addlike(post_id, 'post'); });
    }
    document.getElementById("comments-button").addEventListener("click", function () { showComments(post_id); })
    document.getElementById("post-likes").innerHTML = likes;
    document.getElementById("post-description").innerHTML = description;
}

async function showContent() {
    // poster
    let post = await loadPoster();
    document.getElementById("poster-id").src = "/static/img/uploads/" + post.image;
    const likeButton =  document.getElementById("likes-button");
    if (post.liked) {
        likeButton.addEventListener("click", function() { removelike(post_id, 'post'); });
        likeButton.innerHTML = "&#10084";
    } else {
        likeButton.addEventListener("click", function() { addlike(post_id, 'post'); });
    }
    document.getElementById("comments-button").addEventListener("click", function () { showComments(post_id); })
    let event = await loadEvent(12345);
    document.getElementById("event-name").innerHTML = "Title: " + event.name;
    document.getElementById("event-day").innerHTML = "Date: " + event.starting_date.split(" ")[0];
    document.getElementById("event-time").innerHTML = "Time: " + event.starting_date.split(" ")[1];
    document.getElementById("event-vip").innerHTML = "Vip: " + event.vip;
    document.getElementById("event-maxpeople").innerHTML = "Max people: " + event.max_capacity;
    document.getElementById("event-price").innerHTML = "Price: " + event.price + "€";
    document.getElementById("event-min-age").innerHTML = "Minimum Age: " + event.minimum_age;
    document.getElementById("event-description").innerHTML = "Description: " + post.description;

    // photos
    let photos = await loadPhotos();
    console.log(photos);
    let photo = photos[0];
    let photosDiv = document.getElementById("photos");
    let template = document.getElementById("template-photos");
    if (photos.length == 0) {
        photosDiv.innerHTML = "No posts to show";
        console.log("No posts to show");
    } else {
        let dim = 0;
        for (let photo_index = 0; photo_index < photos.length; photo_index++) {
            let clone = document.importNode(template.content, true);
            clone.querySelector("#photo-id").src = "/static/img/uploads/" + photo.image;
            clone.querySelector("#photo-id").addEventListener("click", function () { openModal(photos[photo_index]); });
            photo = photos[photo_index];
            photosDiv.appendChild(clone);
            dim++;
        }
        let i = 0;
        if (dim % 3 == 1) {
            i = 2;
        } else if (dim % 3 == 2) {
            i = 1;
        }
        for (let j = 0; j < i; j++) {
            let clone = document.importNode(template.content, true);
            clone.querySelector("#photo-id").src = "/static/img/default-image.png";
            clone.querySelector("#photo-id").style.visibility = "hidden";
            photosDiv.appendChild(clone);
        }
    }

    // partecipants
    let partecipants = await loadPartecipations("12345");
    console.log(partecipants);
    let partecipantsDiv = document.getElementById("people");
    template = document.getElementById("template-partecipants");
    if (partecipants.length == 0) {
        partecipantsDiv.innerHTML = "No partecipants to show";
        console.log("No partecipants to show");
    } else {
        for (let partecipant_index = 0; partecipant_index < partecipants.length; partecipant_index++) {
            let clone = document.importNode(template.content, true);
            clone.querySelector("#partecipant-photo").src = "/static/img/uploads/" + partecipants[partecipant_index].user_photo;
            clone.querySelector("#partecipant-name").innerHTML = partecipants[partecipant_index].username;
            partecipantsDiv.appendChild(clone);
        }
    }
}

showContent();