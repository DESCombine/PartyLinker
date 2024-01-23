import { request_path } from "/static/js/config.js?v=1";
import { cleanTemplateList, removeLike, addLike, addEventDescription, loadEvent, showComments, showPartecipations } from "/static/js/utils.js";

async function loadOnlineUsers() {
    const response = await fetch(request_path + "/user/load_online_users.php", {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
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
        clone.querySelector("#online-image").src = "/static/img/uploads/" + user.profile_photo;
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

async function showFeed() {
    const feed = document.getElementById("feed");
    const posts = await loadFeed();
    let template = document.getElementById("post-template");
    for (let i = 0; i < posts.length; i++) {
        let post = posts[i];
        addNewFeedPost(template, feed, post.post_id, post.event_id, post.profile_photo, 
                post.username, post.image, post.description, post.likes, post.event_post, post.liked);
    }
    document.getElementById("comments-modal").addEventListener("hidden.bs.modal", function() { cleanTemplateList("comments"); });
    document.getElementById("partecipants-modal").addEventListener("hidden.bs.modal", function() { cleanTemplateList("partecipants"); });
}

async function addNewFeedPost(template, feed, post_id, event_id, user_photo, username,
    image, description, likes, event, liked) {
    let clone = document.importNode(template.content, true);
    clone.querySelector("#post-id").setAttribute("name", "post" + post_id);
    clone.querySelector("#post-user-photo").src = "/static/img/uploads/" + user_photo;
    clone.querySelector("#post-name").innerHTML = username;
    clone.querySelector("#post-photo").src = "/static/img/uploads/" + image;
    const likeButton = clone.querySelector("#likes-button");
    if (liked) {
        likeButton.addEventListener("click", function() { removeLike(post_id, 'post'); });
        likeButton.innerHTML = "<i class='fa-solid fa-heart text-danger'></i>";
    } else {
        likeButton.addEventListener("click", function() { addLike(post_id, 'post'); });
    }
    clone.querySelector("#comments-button").addEventListener("click", function() { showComments(post_id); });
    clone.querySelector("#post-likes").innerHTML = likes;
    clone.querySelector("#post-description").innerHTML = description;
    if (event) {
        clone.querySelector("#partecipants-button").addEventListener("click", function() { showPartecipations(event_id); });
        clone.querySelector("#partecipants-button").classList.remove("invisible");
        addEventDescription(clone, await loadEvent(event_id));
    } else {
        clone.querySelector("#event-info").innerHTML = "";
        clone.querySelector("#event-button").addEventListener("click", function () { window.location.replace("../event/eventpage.html?id=" + event_id); });
        clone.querySelector("#event-button").classList.remove("invisible");
        const parent = clone.querySelector("#under-post");
        parent.insertBefore(parent.lastElementChild, parent.lastElementChild.previousElementSibling);
        
    }
    feed.appendChild(clone);
}

showOnlineUsers();
showFeed();