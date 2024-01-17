import { request_path } from "/static/js/config.js";

export async function loadUserImage(user_id) {
    const response = await fetch(request_path + "/user/load_user_img.php?user=" + user_id);
    const image = await response.json();
    return image;
}

async function loadEvent(event_id) {
    const response = await fetch(request_path + "/user/load_event.php?event=" + event_id);
    const event = await response.json();
    return event;
}

export async function addNewPost(template, feed, post_id, event_id, user_photo, username,
    image, description, hearts, event, hearted) {
    let clone = document.importNode(template.content, true);
    clone.querySelector("#post-id").setAttribute("name", post_id);
    clone.querySelector("#post-user-photo").src = "/static/img/uploads/" + user_photo;
    clone.querySelector("#post-name").innerHTML = username;
    clone.querySelector("#post-photo").src = "/static/img/uploads/" + image;
    const heartButton = clone.querySelector("#hearts-button");
    if (hearted) {
        heartButton.addEventListener("click", function() { removeHeart(post_id, 'post'); });
        heartButton.innerHTML = "&#10084";
    } else {
        heartButton.addEventListener("click", function() { addHeart(post_id, 'post'); });
    }
    clone.querySelector("#comments-button").addEventListener("click", function() { showComments(post_id, username); });
    clone.querySelector("#post-hearts").innerHTML = hearts;
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

async function addHeart(heart_id, type) {
    heart(heart_id, type, "/user/upload_heart.php", 1);
}

async function removeHeart(heart_id, type) {
    heart(heart_id, type, "/user/remove_heart.php", -1);
}

async function heart(heart_id, type, request, addOrRemove) {
    await fetch(request_path + request, {
        method: "POST",
        credentials: "include",
        header: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            "heart_id": heart_id,
            "type": type
        })
    });
    let hearts;
    let heartButton;
    const element = document.getElementsByName(heart_id)[0];
    switch (type) {
        case 'post':
            hearts = element.querySelector("#post-hearts");
            heartButton = element.querySelector("#hearts-button");
            break;
        case 'comment':
            hearts = element.querySelector("#comment-hearts");
            heartButton = element.querySelector("#comment-heart-bt");
            break;
    }
    hearts.innerHTML = parseInt(hearts.innerHTML) + addOrRemove;
    let addFun;
    let removeFun;
    if (addOrRemove == 1) {
        heartButton.innerHTML = "&#10084";
        addFun = function() { removeHeart(heart_id, type); };
        removeFun = function() { addHeart(heart_id, type); };
    } else {
        heartButton.innerHTML = "&#129293";
        addFun = function() { addHeart(heart_id, type); };
        removeFun = function() { removeHeart(heart_id, type); };
    }
    heartButton.removeEventListener("click", removeFun);
    heartButton.addEventListener("click", addFun);
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
        const heartButton = clone.querySelector("#comment-heart-bt");
        if (comment.hearted) {
            heartButton.addEventListener("click", function() { removeHeart(comment.comment_id, 'comment'); });
            heartButton.innerHTML = "&#10084";
        } else {
            heartButton.addEventListener("click", function() { addHeart(comment.comment_id, 'comment'); });
        }
        clone.querySelector("#comment-hearts").innerHTML = comment.hearts;
        comments.appendChild(clone);
    }
    const comment_button = document.querySelector("#submit-comment");
    const comment_input = document.querySelector("#comment-input");
    comment_button.addEventListener("click", function() { submitComment(post_id, comment_input); });
}

async function loadPartecipations(event_id) {
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