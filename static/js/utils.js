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
    image, description, hearts, event) {
    let clone = document.importNode(template.content, true);
    clone.querySelector("#post-id").setAttribute("name", post_id);
    clone.querySelector("#post-user-photo").src = "/static/img/uploads/" + user_photo;
    clone.querySelector("#post-name").innerHTML = username;
    clone.querySelector("#post-photo").src = "/static/img/uploads/" + image;
    clone.querySelector("#hearts-button").addEventListener("click", function() { heartPost(post_id); });
    clone.querySelector("#comments-button").addEventListener("click", function() { showComments(post_id); });
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

async function heartPost(post_id) {
    await fetch(request_path + "/user/upload_post_heart.php", {
        method: "POST",
        credentials: "include",
        header: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            "post_id": post_id
        })
    });
    const post = document.getElementsByName(post_id)[0];
    const hearts = post.querySelector("#post-hearts");
    hearts.innerHTML = parseInt(hearts.innerHTML) + 1;
}

async function postComment(post_id, content) {
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

async function loadComments(post_id) {
    const response = await fetch(request_path + "/user/load_comments.php?post=" + post_id);
    const comments = await response.json();
    return comments;
}

async function showComments(post_id) {
    const comments = document.getElementById("comments");
    const comments_to_show = await loadComments(post_id);
    let template = document.getElementById("comment-template");
    for (let i = 0; i < comments_to_show.length; i++) {
        let comment = comments_to_show[i];
        let clone = document.importNode(template.content, true);
        clone.querySelector("#comment-user-photo").src = "/static/img/uploads/" + comment.profile_photo;
        clone.querySelector("#comment-name").textContent = comment.username;
        clone.querySelector("#comment-content").textContent = comment.content;
        clone.querySelector("#comment-hearts").innerHTML = comment.hearts;
        comments.appendChild(clone);
    }
}

async function loadPartecipations(event_id) {
    const response = await fetch(request_path + "/user/load_partecipations.php?event=" + event_id);
    const partecipations = await response.json();
    return partecipations;
}

async function showPartecipations(event_id) {
    const partecipations = document.getElementById("partecipants");
    const partecipations_list = await loadPartecipations(event_id);
    let template = document.getElementById("partecipants-template");
    let isUserPartecipating = false;
    for (let i = 0; i < partecipations_list.length; i++) {
        let partecipation = partecipations_list[i];
        if (partecipation.current_user) {
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