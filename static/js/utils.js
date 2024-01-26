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
    const element = document.getElementsByName(type+like_id)[0];
    const likes = element.querySelector(".likes");
    const likeButton = element.querySelector(".like-button");
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

async function loadComments(post_id) {
    const response = await fetch(request_path + "/user/load_comments.php?post=" + post_id);
    const comments = await response.json();
    return comments;
}

async function submitComment(post_id) {
    const modalFooter = document.querySelector("#comments-modal .modal-footer");
    const content = modalFooter.querySelector("input").value;
    modalFooter.querySelector("input").value = "";
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
    cleanTemplateList(document.querySelector("#comments-modal ol"));
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
    cleanTemplateList(document.querySelector("#comments-modal ol"));
    showComments(post_id);
}

export async function showComments(post_id) {
    const comModal = document.querySelector("#comments-modal");
    const comments = comModal.querySelector("ol");
    const comments_to_show = await loadComments(post_id);
    const template = comModal.querySelector("template");
    for (let i = 0; i < comments_to_show.length; i++) {
        let comment = comments_to_show[i];
        let clone = template.content.cloneNode(true);
        clone.querySelector("li").setAttribute("name", "comment" + comment.comment_id);
        clone.querySelector("img").src = "/static/img/uploads/" + comment.profile_photo;
        clone.querySelector("h3").textContent = comment.username;
        clone.querySelector(".content").textContent = comment.content;
        if (comment.owner) {
            clone.querySelector(".trash-button").classList.remove("invisible");
            clone.querySelector(".trash-button").addEventListener("click", function() { removeComment(comment.comment_id, post_id); })
        }
        const likeButton = clone.querySelector(".like-button");
        if (comment.liked) {
            likeButton.addEventListener("click", function() { removeLike(comment.comment_id, 'comment'); });
            likeButton.innerHTML = "<i class='fa-solid fa-heart text-danger'></i>";
        } else {
            likeButton.addEventListener("click", function() { addLike(comment.comment_id, 'comment'); });
        }
        clone.querySelector(".likes").innerHTML = comment.likes;
        comments.appendChild(clone);
    }
    const modalFooter = comModal.querySelector(".modal-footer");
    const comment_button = modalFooter.querySelector("button");
    resetEventListener(comment_button, function() { submitComment(post_id); });
}

export async function loadPartecipations(event_id) {
    const response = await fetch(request_path + "/user/load_partecipations.php?event=" + event_id);
    const partecipations = await response.json();
    return partecipations;
}

async function partecipationClick(event_id, request) {
    await fetch(request_path + request, {
        method: "POST",
        credentials: "include",
        header: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            "event_id": event_id
        })
    });
    const partList = document.querySelector("#partecipants-modal ul");
    cleanTemplateList(partList);
    showPartecipations(event_id);
}

export async function showPartecipations(event_id) {
    const partModal = document.querySelector("#partecipants-modal")
    const partecipations = partModal.querySelector("ul");
    const partecipations_list = await loadPartecipations(event_id);
    let template = partecipations.querySelector("template");
    let isUserPartecipating = false;
    for (let i = 0; i < partecipations_list.length; i++) {
        let partecipation = partecipations_list[i];
        if (partecipation.partecipating) {
            isUserPartecipating = true;
        }
        let clone = template.content.cloneNode(true);
        clone.querySelector("img").src = "/static/img/uploads/" + partecipation.profile_photo;
        clone.querySelector("h3").textContent = partecipation.username;
        partecipations.appendChild(clone);
    }
    const partecipants_button = partModal.getElementsByTagName("button")[0];
    const busy_button = partModal.getElementsByTagName("button")[1];
    resetEventListener(partecipants_button, function() { 
            partecipationClick(event_id, "/user/upload_partecipation.php"); }).disabled = isUserPartecipating;
    resetEventListener(busy_button, function() { 
            partecipationClick(event_id, "/user/remove_partecipation.php"); }).disabled = !isUserPartecipating;
}