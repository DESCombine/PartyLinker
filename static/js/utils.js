import { request_path } from "/static/js/config.js?v=2";

export function checkError(response) {
    if (response.error === "No token provided") {
        window.location.replace("/login/login.html");
    }
} 

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
    checkError(settings);
    return settings.organizer;
}

export function cleanTemplateList(list) {
    while (list.getElementsByTagName('li').length > 0) {
        list.removeChild(list.lastChild);
    }
}

function resetEventListener(oldButton, fun) {
    const newButton = oldButton.cloneNode(true);
    oldButton.parentNode.replaceChild(newButton, oldButton);
    newButton.addEventListener("click", fun);
    return newButton;
}

export function addEventDescription(parent, event) {
    parent.querySelector("h4").innerHTML = event.name;
    const infos = parent.querySelector("ol");
    infos.innerHTML = `
            <li class="list-group-item"><p>Place ${event.location}</p></li>
            <li class="list-group-item"><p>Starting Date: ${event.starting_date}</p></li>
            <li class="list-group-item"><p>Ending Date: ${event.ending_date}</p></li>
            <li class="list-group-item"><p>Vips: ${event.vips}</p></li>
            <li class="list-group-item"><p>Max-People: ${event.max_capacity}</p></li>
            <li class="list-group-item"><p>Price: ${event.price}</p></li>
            <li class="list-group-item"><p>Minimum age: ${event.minimum_age}</p></li>`;
}

export async function addLike(like_id, type) {
    like(like_id, type, "/user/upload_like.php", 1);
}

export async function removeLike(like_id, type) {
    like(like_id, type, "/user/remove_like.php", -1);
}

async function like(like_id, type, request, addOrRemove) {
    const response = await fetch(request_path + request, {
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
    checkError(await response.json());
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
    likes.innerHTML = parseInt(likes.innerHTML) + addOrRemove;
    let fun;
    if (addOrRemove == 1) {
        likeButton.innerHTML = "<i class='fa-solid fa-heart text-danger'></i>";
        fun = function() { removeLike(like_id, type); };
    } else {
        likeButton.innerHTML = "<i class='fa-regular fa-heart'></i>";
        fun = function() { addLike(like_id, type); };
    }
    resetEventListener(likeButton, fun);
}

async function submitComment(post_id) {
    const content = document.querySelector("#comment-input").value;
    document.querySelector("#comment-input").value = "";
    const response = await fetch(request_path + "/user/upload_comment.php", {
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
    checkError(await response.json());
    cleanTemplateList(document.querySelector("comments"));
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
    cleanTemplateList(document.querySelector("comments"));
    showComments(post_id);
}

async function submitPartecipation(event_id) {
    const response = await fetch(request_path + "/user/upload_partecipation.php", {
        method: "POST",
        credentials: "include",
        header: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            "event_id": event_id
        })
    });
    checkError(await response.json());
    cleanTemplateList(document.querySelector("partecipants"));
    showPartecipations(event_id);
}

async function submitBusy(event_id) {
    const response = await fetch(request_path + "/user/remove_partecipation.php", {
        method: "POST",
        credentials: "include",
        header: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            "event_id": event_id
        })
    });
    checkError(await response.json());
    cleanTemplateList(document.querySelector("partecipants"));
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
            likeButton.addEventListener("click", function() { removeLike(comment.comment_id, 'comment'); });
            likeButton.innerHTML = "<i class='fa-solid fa-heart text-danger'></i>";
        } else {
            likeButton.addEventListener("click", function() { addLike(comment.comment_id, 'comment'); });
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