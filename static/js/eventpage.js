import { request_path } from "/static/js/config.js?v=2";
import { loadEvent, showComments, resetEventListener, cleanTemplateList, translatePost, loadPartecipations, loadUserImage } from "/static/js/utils.js?v=2";
const event_id = new URLSearchParams(window.location.search).get('id');
console.log(event_id);
const post_id = await loadPostId(event_id);

async function loadPostId(event_id) {
    const response = await fetch(request_path + "/user/load_post_id.php?event=" + event_id);
    const post_id = await response.json();
    return post_id;
}
async function loadPoster() {
    const response = await fetch(request_path + "/user/load_event_poster.php?event=" + event_id);
    const poster = await response.json();
    return poster;
}

async function loadPhotos() {
    const response = await fetch(request_path + "/user/load_event_posts.php?event=" + event_id);
    const photos = await response.json();
    return photos;
}

function openModal(post) {
    console.log(post.post_id);
    const modal = document.getElementById("post-modal");
    showModalPost(modal, post.post_id, post.event_id, post.user_photo, post.username, post.image, post.description, post.likes, post.event_post, post.liked);
}


async function showModalPost(modal, post_id, event_id, user_photo, username,
    image, description, likes, event, liked) {
    const postContent = modal.querySelector(".modal-content");
    const postActions = postContent.querySelector("ol");
    // clean buttons event listeners
    document.getElementById("translate").replaceWith(document.getElementById("translate").cloneNode(true));
    document.getElementById("comments-button-modal").replaceWith(document.getElementById("comments-button-modal").cloneNode(true));
    document.getElementById("likes-button-modal").replaceWith(document.getElementById("likes-button-modal").cloneNode(true));
    // show modal
    document.getElementById("post-user-photo").src = "/static/img/uploads/" + await loadUserImage(username);
    document.getElementById("post-name").innerHTML = username;
    document.getElementById("post-photo").src = "/static/img/uploads/" + image;
    document.getElementById("post-likes-modal").innerHTML = likes;
    document.getElementById("post-description").innerHTML = description;
    document.getElementById("translate").addEventListener("click", function () { translatePost(post_id, document.getElementById("post-description")); });
    const likeButton = postActions.querySelector("#likes-button-modal");
    if (liked) {
        likeButton.addEventListener("click", function() { removeLike(post_id, 'post', document.getElementById('likes-button-modal'), document.getElementById('post-likes-modal')); });
        likeButton.innerHTML = "<i class='fa-solid fa-heart text-danger'></i>";
    } else {
        likeButton.addEventListener("click", function() { addLike(post_id, 'post', document.getElementById('likes-button-modal'), document.getElementById('post-likes-modal')); });
    }
    postActions.querySelector("#comments-button-modal").addEventListener("click", function() { showComments(post_id); });
}

export async function addLike(like_id, type, likeButton, likes) {
    like(like_id, type, "/user/upload_like.php", 1, likeButton, likes);
}

export async function removeLike(like_id, type, likeButton, likes) {

    like(like_id, type, "/user/remove_like.php", -1, likeButton, likes);
}

async function like(like_id, type, request, addOrRemove, likeButton, likes) {
    await fetch(request_path + request, {
        method: "POST",
        credentials: "include",
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            "like_id": like_id,
            "type": type
        })
    });
    likes.innerHTML = parseInt(likes.innerHTML) + addOrRemove;
    let fun;
    if (addOrRemove == 1) {
        likeButton.innerHTML = "<i class='fa-solid fa-heart text-danger'></i>";
        fun = function() { removeLike(like_id, type, likeButton, likes); };
    } else {
        likeButton.innerHTML = "<i class='fa-regular fa-heart'></i>";
        fun = function() { addLike(like_id, type, likeButton, likes); };
    }
    console.log(likeButton);
    likeButton.replaceWith((likeButton).cloneNode(true));
}

async function showContent() {
    // poster
    let post = await loadPoster();
    document.getElementById("poster-id").src = "/static/img/uploads/" + post.image;
    const likeButton = document.getElementById("likes-button");
    if (post.liked) {
        likeButton.addEventListener("click", function () { removeLike(post_id, 'post', document.getElementById('likes-button'), document.getElementById('event-likes')); });
        likeButton.innerHTML = "&#10084";
    } else {
        likeButton.addEventListener("click", function () { addLike(post_id, 'post', document.getElementById('likes-button'), document.getElementById('event-likes')); });
    }
    document.getElementById("comments-button").addEventListener("click", function () { showComments(post_id); })
    let event = await loadEvent(event_id);
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
    let partecipants = await loadPartecipations(event_id);
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