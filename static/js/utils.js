import { request_path } from "/static/js/config.js";

export async function loadUserImage(user_id) {
    const response = await fetch(request_path + "/user/load_user_img.php?user=" + user_id);
    const image = await response.json();
    return image;
}

export async function loadEvent(event_id) {
    const response = await fetch(request_path + "/user/load_event.php?event=" + event_id);
    const event = await response.json();
    return event;
}

export async function addNewPost(template, feed, post_id, event_id, user_photo, username,
    image, description, likes, event, liked) {
    let clone = document.importNode(template.content, true);
    clone.querySelector("#post-id").setAttribute("name", post_id);
    clone.querySelector("#post-user-photo").src = "/static/img/uploads/" + user_photo;
    clone.querySelector("#post-name").innerHTML = username;
    clone.querySelector("#post-photo").src = "/static/img/uploads/" + image;
    const likeButton = clone.querySelector("#likes-button");
    if (liked) {
        likeButton.addEventListener("click", function() { removelike(post_id, 'post'); });
        likeButton.innerHTML = "&#10084";
    } else {
        likeButton.addEventListener("click", function() { addlike(post_id, 'post'); });
    }
    clone.querySelector("#comments-button").addEventListener("click", function() { showComments(post_id, username); });
    clone.querySelector("#post-likes").innerHTML = likes;
    clone.querySelector("#post-description").innerHTML = description;
    if (event) {
        clone.querySelector("#partecipants-button").addEventListener("click", function() { showPartecipations(event_id); });
        clone.querySelector("#partecipants-button").classList.remove("invisible");
        addEventDescription(clone, await loadEvent(event_id));
    }
    feed.appendChild(clone);
}

function addEventDescription(post, event) {
    let template = document.getElementById("description-template");
    let clone = document.importNode(template.content, true);
    clone.querySelector("#event-name").innerHTML = event.name;
    clone.querySelector("#event-place").innerHTML = "Place: " + event.location;
    clone.querySelector("#event-date").innerHTML = "Starting Date: " + event.starting_date + "<br>Ending Date: " + event.ending_date;
    clone.querySelector("#event-vips").innerHTML = "Vips: " + event.vips;
    clone.querySelector("#event-people").innerHTML = "Available places: " + event.max_capacity;
    clone.querySelector("#event-price").innerHTML = "Price: " + event.price;
    clone.querySelector("#event-age").innerHTML = "Required age: " + event.minimum_age;
    post.appendChild(clone);
}

async function addlike(like_id, type) {
    like(like_id, type, "/user/upload_like.php", 1);
}

async function removelike(like_id, type) {
    like(like_id, type, "/user/remove_like.php", -1);
}

async function like(like_id, type, request, addOrRemove) {
    await fetch(request_path + request, {
        method: "POST",
        credentials: "include",
        header: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            "like_id": like_id,
            "type": type
        })
    });
    let likes;
    let likeButton;
    const element = document.getElementsByName(like_id)[0];
    switch (type) {
        case 'post':
            likes = element.querySelector("#post-likes");
            likeButton = element.querySelector("#likes-button");
            break;
        case 'comment':
            likes = element.querySelector("#comment-likes");
            likeButton = element.querySelector("#comment-like-bt");
            break;
    }
    likes.innerHTML = parseInt(likes.innerHTML) + addOrRemove;
    let addFun;
    let removeFun;
    if (addOrRemove == 1) {
        likeButton.innerHTML = "&#10084";
        addFun = function() { removelike(like_id, type); };
        removeFun = function() { addlike(like_id, type); };
    } else {
        likeButton.innerHTML = "&#129293";
        addFun = function() { addlike(like_id, type); };
        removeFun = function() { removelike(like_id, type); };
    }
    likeButton.removeEventListener("click", removeFun);
    likeButton.addEventListener("click", addFun);
}

async function submitComment(post_id, content) {
    content = content.value;
    await fetch(request_path + "/user/upload_comment.php", {
        method: "POST",
        credentials: "include",
        header: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            "post_id": post_id,
            "content": content
        })
    });
}

async function removeComment(comment_id) {
    await fetch(request_path + "/user/remove_comment.php", {
        method: "POST",
        credentials: "include",
        header: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            "comment_id": comment_id
        })
    });
}

async function submitPartecipation(event_id) {
    await fetch(request_path + "/user/upload_partecipation.php", {
        method: "POST",
        credentials: "include",
        header: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            "event_id": event_id
        })
    });
    document.querySelector("#submit-partecipation").disabled = true;
    document.querySelector("#submit-busy").disabled = false;
}

async function submitBusy(event_id) {
    await fetch(request_path + "/user/remove_partecipation.php", {
        method: "POST",
        credentials: "include",
        header: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            "event_id": event_id
        })
    });
    document.querySelector("#submit-partecipation").disabled = true;
    document.querySelector("#submit-busy").disabled = false;
}

async function getCurrentUser() {
    const response = await fetch(request_path + "/user/load_current_username.php", {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    });
    const user = await response.json();
    return user;
}

async function loadComments(post_id) {
    const response = await fetch(request_path + "/user/load_comments.php?post=" + post_id);
    const comments = await response.json();
    return comments;
}

export async function showComments(post_id, poster) {
    const comments = document.getElementById("comments");
    const comments_to_show = await loadComments(post_id);
    const current_user = getCurrentUser();
    let template = document.getElementById("comment-template");
    for (let i = 0; i < comments_to_show.length; i++) {
        let comment = comments_to_show[i];
        let clone = document.importNode(template.content, true);
        clone.querySelector("#comment-id").setAttribute("name", comment.comment_id);
        clone.querySelector("#comment-user-photo").src = "/static/img/uploads/" + comment.profile_photo;
        clone.querySelector("#comment-name").textContent = comment.username;
        clone.querySelector("#comment-content").textContent = comment.content;
        if (comment.username == current_user || poster == current_user) {
            clone.querySelector("#comment-trash").classList.remove("invisible");
            clone.querySelector("#comment-trash").addEventListener("click", function() { removeComment(comment.comment_id); })
        }
        const likeButton = clone.querySelector("#comment-like-bt");
        if (comment.liked) {
            likeButton.addEventListener("click", function() { removelike(comment.comment_id, 'comment'); });
            likeButton.innerHTML = "&#10084";
        } else {
            likeButton.addEventListener("click", function() { addlike(comment.comment_id, 'comment'); });
        }
        clone.querySelector("#comment-likes").innerHTML = comment.likes;
        comments.appendChild(clone);
    }
    const comment_button = document.querySelector("#submit-comment");
    const comment_input = document.querySelector("#comment-input");
    comment_button.addEventListener("click", function() { submitComment(post_id, comment_input); });
}

export async function loadPartecipations(event_id) {
    const response = await fetch(request_path + "/user/load_partecipations.php?event=" + event_id);
    const partecipations = await response.json();
    return partecipations;
}

export async function showPartecipations(event_id) {
    const partecipations = document.getElementById("partecipants");
    const partecipations_list = await loadPartecipations(event_id);
    const current_user = getCurrentUser();
    let template = document.getElementById("partecipants-template");
    let isUserPartecipating = false;
    for (let i = 0; i < partecipations_list.length; i++) {
        let partecipation = partecipations_list[i];
        if (partecipation.username == current_user) {
            isUserPartecipating = true;
        }
        let clone = document.importNode(template.content, true);
        clone.querySelector("#partecipants-photo").src = "/static/img/uploads/" + partecipation.profile_photo;
        clone.querySelector("#partecipants-name").textContent = partecipation.username;
        partecipations.appendChild(clone);
    }
    const partecipants_button = document.querySelector("#submit-partecipation");
    const busy_button = document.querySelector("#submit-busy");
    partecipants_button.addEventListener("click", function() { submitPartecipation(event_id); });
    busy_button.addEventListener("click", function() { submitBusy(event_id); });
    partecipants_button.disabled = isUserPartecipating;
    busy_button.disabled = !isUserPartecipating;
}