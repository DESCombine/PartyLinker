import { request_path } from "/static/js/config.js";

async function loadOnlineUsers() {
    const response = await fetch(request_path + "/user/load_online_users.php", {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        include: "credentials"
    });
    const users = await response.json();
    return users;
}

async function showOnlineUsers() {
    const online_users = document.getElementById("online-users");
    const users = await loadOnlineUsers();
    let template = document.querySelector("#online-template");
    for (let i = 0; i < users.length; i++) {
        let user = users[i];
        let clone = document.importNode(template.content, true);
        clone.querySelector("#online-image").src = "/static/img/uploads/" + user.photo;
        clone.querySelector("#online-image").alt = user.username;
        online_users.appendChild(clone);
    }
}

async function loadFeed() {
    const response = await fetch(request_path + "/user/load_feed.php", {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    });
    const posts = await response.json();
    return posts;
}

async function loadUserImage(user_id) {
    const response = await fetch(request_path + "/user/load_user_img.php?user=" + user_id);
    const image = await response.json();
    return image;
}

async function loadEvent(event_id) {
    const response = await fetch(request_path + "/user/load_event.php?event=" + event_id);
    const event = await response.json();
    return event;
}

async function showFeed() {
    const feed = document.getElementById("feed");
    const posts = await loadFeed();
    let template = document.getElementById("post-template");
    for (let i = 0; i < posts.length; i++) {
        let post = posts[i];
        addNewPost(template, feed, post.post_id, post.event_id, await loadUserImage(post.username), 
                post.username, post.image, post.description, post.likes, post.event_post);
    }
}

async function addNewPost(template, feed, post_id, event_id, user_photo, username,
    image, description, likes, event) {
    let clone = document.importNode(template.content, true);
    clone.querySelector("#post-id").setAttribute("name", post_id);
    clone.querySelector("#post-user-photo").src = "/static/img/uploads/" + user_photo;
    clone.querySelector("#post-name").innerHTML = username;
    clone.querySelector("#post-photo").src = "/static/img/uploads/" + image;
    clone.querySelector("#likes-button").onclick = function() { likePost(post_id); };
    clone.querySelector("#comments-button").onclick = function() { showComments(post_id); }
    clone.querySelector("#post-likes").innerHTML = likes;
    clone.querySelector("#post-description").innerHTML = description;
    if (event) {
        clone.querySelector("#partecipants-button").onclick = function() { showPartecipations(event_id); }
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

async function likePost(post_id) {
    const response = await fetch(request_path + "/user/like_post.php?post=" + post_id, {
        method: "GET",
    });
    const post = document.getElementsByName(post_id)[0];
    const likes = post.querySelector("#post-likes");
    likes.innerHTML = parseInt(likes.innerHTML) + 1;
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
    let clone = document.importNode(template.content, true);
    for (let i = 0; i < comments_to_show.length; i++) {
        let comment = comments_to_show[i];
        clone.querySelector("#comment-user-photo").src = "/static/img/uploads/" + await loadUserImage(comment.username);
        clone.querySelector("#comment-name").textContent = comment.username;
        clone.querySelector("#comment-content").textContent = comment.content;
        clone.querySelector("#comment-likes").innerHTML = comment.likes;
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
        clone.querySelector("#partecipation-user-photo").src = "/static/img/uploads/" + await loadUserImage(partecipation.partecipant);
        clone.querySelector("#partecipation-name").textContent = partecipation.partecipant;
        partecipations.appendChild(clone);
    }
}

showOnlineUsers();
showFeed();