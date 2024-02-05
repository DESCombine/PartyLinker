import { request_path } from "/static/js/config.js?v=9";
import { loadUserImage, loadEvent, showComments, resetEventListener, 
        translatePost, loadPartecipations, addEventDescription, cleanTemplateList } from "/static/js/utils.js?v=9";

const event_id = new URLSearchParams(window.location.search).get('id');
const post = await loadPostEvent(event_id);
const event = await loadEvent(event_id);

async function loadPostEvent(event_id) {
    const response = await fetch(request_path + "/user/load_post_event.php?event=" + event_id, {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    
    });
    const post_id = await response.json();
    return post_id;
}

async function loadPhotos() {
    const response = await fetch(request_path + "/user/load_event_posts.php?event=" + event_id, {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    
    });
    const photos = await response.json();
    return photos;
}

async function showContent() {
    // poster
    const poster = document.getElementById("event-poster");
    poster.querySelector("img").src = "/static/img/uploads/" + post.image;
    poster.querySelector("#poster-likes").innerHTML = post.likes;
    const likeButton = document.getElementById("poster-like-bt");
    if (post.liked) {
        likeButton.addEventListener("click", function () { removePosterLike(post.post_id, "post") });
        likeButton.innerHTML = "<em class='fa-solid fa-heart'></em>";
    } else {
        likeButton.addEventListener("click", function () { addPosterLike(post.post_id, "post") });
    }
    document.querySelector("#poster-comment-bt").addEventListener("click", function () { showComments(post.post_id); })
    let desc = poster.querySelector("#poster-description");
    desc.innerHTML = post.description;
    poster.querySelector("#poster-translate-bt").addEventListener("click", function () { translatePost(post.post_id, desc); });
    addEventDescription(poster.querySelector("#poster-info"), event);
    document.getElementById("comments-modal").addEventListener("hidden.bs.modal", 
            function() { cleanTemplateList(document.querySelector("#comments-modal ol")); });
    // photos
    showPhotos(await loadPhotos());

    // partecipants
    let partecipants = await loadPartecipations(event_id);
    let partecipantsDiv = document.getElementById("people");
    let template = document.getElementById("template-partecipants");
    if (partecipants.length == 0) {
        partecipantsDiv.innerHTML = "No partecipants to show";
    } else {
        for (let partecipant_index = 0; partecipant_index < partecipants.length; partecipant_index++) {
            let clone = document.importNode(template.content, true);
            clone.querySelector("#partecipant-photo").src = "/static/img/uploads/" + partecipants[partecipant_index].profile_photo;
            clone.querySelector("#partecipant-name").href = "/profile?user=" + partecipants[partecipant_index].username;
            clone.querySelector("#partecipant-name").innerHTML = partecipants[partecipant_index].username;
            partecipantsDiv.appendChild(clone);
        }
    }
}

function openModal(post) {
    showModalPost(post.post_id, post.event_id, post.username, post.image,
            post.description, post.likes, post.liked);
}

async function showModalPost(post_id, event_id, username,
        image, description, likes, liked) {
    const postActions = document.getElementById("post-actions");
    // clean buttons event listeners
    document.getElementById("translate").replaceWith(document.getElementById("translate").cloneNode(true));
    document.getElementById("comments-button").replaceWith(document.getElementById("comments-button").cloneNode(true));
    document.getElementById("likes-button").replaceWith(document.getElementById("likes-button").cloneNode(true));
    // show modal
    document.getElementById("post-user-photo").src = "/static/img/uploads/" + await loadUserImage(username);
    document.getElementById("post-name").innerHTML = username;
    document.getElementById("post-photo").src = "/static/img/uploads/" + image;
    document.getElementById("post-likes").innerHTML = likes;
    document.getElementById("post-description").innerHTML = description;
    document.getElementById("translate").addEventListener("click", function () { translatePost(post_id, document.getElementById("post-description")); });
    const likeButton = postActions.querySelector("#likes-button");
    if (liked) {
        likeButton.addEventListener("click", function () { removeModalLike(post_id, "post") });
        likeButton.innerHTML = "<em class='fa-solid fa-heart'></em>";
    } else {
        likeButton.addEventListener("click", function () { addModalLike(post_id, "post") });
    }
    postActions.querySelector("#comments-button").addEventListener("click", function() { showComments(post_id); });
    postActions.querySelector("a").href = "/event/eventpage.html?id=" + event_id;
}

function showPhotos(photos) {
    let photo = photos[0];
    let photosDiv = document.getElementById("photos");
    let template = document.getElementById("template-photos");
    if (photos.length == 0) {
        photosDiv.innerHTML = "No posts to show";
    } else {
        let dim = 0;
        for (let photo_index = 0; photo_index < photos.length; photo_index++) {
            photo = photos[photo_index];
            let clone = document.importNode(template.content, true);
            clone.querySelector("#photo-id").src = "/static/img/uploads/" + photo.image;
            clone.querySelector("#photo-id").addEventListener("click", function () { openModal(photos[photo_index]); });
            
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
            clone.querySelector("div").style.visibility = "hidden";
            clone.querySelector("div").classList.add("invisible");
            photosDiv.appendChild(clone);
        }
    }
}

async function addPosterLike(like_id, type) {
    likePoster(like_id, type, "/user/upload_like.php", 1);
}

async function removePosterLike(like_id, type) {
    likePoster(like_id, type, "/user/remove_like.php", -1);
}

async function likePoster(like_id, type, request, addOrRemove) {
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
    const likes = document.getElementById("poster-likes");
    const likeButton = document.getElementById("poster-like-bt");
    likes.innerHTML = parseInt(likes.innerHTML) + addOrRemove;
    let fun;
    if (addOrRemove == 1) {
        likeButton.innerHTML = "<em class='fa-solid fa-heart'></em>";
        fun = function() { removePosterLike(like_id, type, likeButton, likes); };
    } else {
        likeButton.innerHTML = "<em class='fa-regular fa-heart'></em>";
        fun = function() { addPosterLike(like_id, type, likeButton, likes); };
    }
    resetEventListener(likeButton, fun);
}

async function addModalLike(like_id, type) {
    likeModal(like_id, type, "/user/upload_like.php", 1);
}

async function removeModalLike(like_id, type) {
    likeModal(like_id, type, "/user/remove_like.php", -1);
}

async function likeModal(like_id, type, request, addOrRemove) {
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
    const likes = document.getElementById("post-likes");
    const likeButton = document.getElementById("likes-button");
    likes.innerHTML = parseInt(likes.innerHTML) + addOrRemove;
    let fun;
    if (addOrRemove == 1) {
        likeButton.innerHTML = "<em class='fa-solid fa-heart'></em>";
        fun = function() { removeModalLike(like_id, type, likeButton, likes); };
    } else {
        likeButton.innerHTML = "<em class='fa-regular fa-heart'></em>";
        fun = function() { addModalLike(like_id, type, likeButton, likes); };
    }
    resetEventListener(likeButton, fun);
}

showContent();