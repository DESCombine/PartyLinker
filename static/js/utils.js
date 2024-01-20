import { request_path } from "/static/js/config.js?v=1";

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

export async function checkOrganizer() {
    const response = await fetch(request_path + "/user/load_settings.php");
    const settings = await response.json();
    return settings.organizer;
}

export function cleanTemplateList(listId) {
    const modalList = document.getElementById(listId);
    while (modalList.getElementsByTagName('li').length > 0) {
        modalList.removeChild(modalList.lastChild);
    }
}

function resetEventListener(oldButton, fun) {
    const newButton = oldButton.cloneNode(true);
    oldButton.parentNode.replaceChild(newButton, oldButton);
    newButton.addEventListener("click", fun);
    return newButton;
}

export function addEventDescription(post, event) {
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

export async function addlike(like_id, type) {
    like(like_id, type, "/user/upload_like.php", 1);
}

export async function removelike(like_id, type) {
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
    const element = document.getElementsByName(type+like_id)[0];
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
    console.log(element);
    likes.innerHTML = parseInt(likes.innerHTML) + addOrRemove;
    let fun;
    if (addOrRemove == 1) {
        likeButton.innerHTML = "&#10084";
        fun = function() { removelike(like_id, type); };
    } else {
        likeButton.innerHTML = "&#129293";
        fun = function() { addlike(like_id, type); };
    }
    resetEventListener(likeButton, fun);
}

async function submitComment(post_id) {
    const content = document.querySelector("#comment-input").value;
    document.querySelector("#comment-input").value = "";
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
    cleanTemplateList("comments");
    showComments(post_id);
}

async function removeComment(comment_id, post_id) {
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
    cleanTemplateList("comments");
    showComments(post_id);
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
    cleanTemplateList("partecipants");
    showPartecipations(event_id);
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
    cleanTemplateList("partecipants");
    showPartecipations(event_id);
}

async function loadComments(post_id) {
    const response = await fetch(request_path + "/user/load_comments.php?post=" + post_id);
    const comments = await response.json();
    return comments;
}

export async function showComments(post_id) {
    const comments = document.getElementById("comments");
    const comments_to_show = await loadComments(post_id);
    let template = document.getElementById("comment-template");
    for (let i = 0; i < comments_to_show.length; i++) {
        let comment = comments_to_show[i];
        let clone = document.importNode(template.content, true);
        clone.querySelector("#comment-id").setAttribute("name", "comment"+comment.comment_id);
        clone.querySelector("#comment-user-photo").src = "/static/img/uploads/" + comment.profile_photo;
        clone.querySelector("#comment-name").textContent = comment.username;
        clone.querySelector("#comment-content").textContent = comment.content;
        if (comment.owner) {
            clone.querySelector("#comment-trash").classList.remove("invisible");
            clone.querySelector("#comment-trash").addEventListener("click", function() { removeComment(comment.comment_id, post_id); })
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
    resetEventListener(comment_button, function() { submitComment(post_id); });
}

export async function loadPartecipations(event_id) {
    const response = await fetch(request_path + "/user/load_partecipations.php?event=" + event_id);
    const partecipations = await response.json();
    return partecipations;
}

export async function showPartecipations(event_id) {
    const partecipations = document.getElementById("partecipants");
    const partecipations_list = await loadPartecipations(event_id);
    let template = document.getElementById("partecipants-template");
    let isUserPartecipating = false;
    for (let i = 0; i < partecipations_list.length; i++) {
        let partecipation = partecipations_list[i];
        if (partecipation.partecipating) {
            isUserPartecipating = true;
        }
        let clone = document.importNode(template.content, true);
        clone.querySelector("#partecipants-photo").src = "/static/img/uploads/" + partecipation.profile_photo;
        clone.querySelector("#partecipants-name").textContent = partecipation.username;
        partecipations.appendChild(clone);
    }
    const partecipants_button = document.querySelector("#submit-partecipation");
    const busy_button = document.querySelector("#submit-busy");
    resetEventListener(partecipants_button, function() { submitPartecipation(event_id); }).disabled = isUserPartecipating;
    resetEventListener(busy_button, function() { submitBusy(event_id); }). disabled = !isUserPartecipating;
}