import { request_path } from "/static/js/config.js";
import { loadUserImage, addNewPost } from "/static/js/utils.js";

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
        addNewPost(template, feed, post.post_id, post.event_id, await loadUserImage(post.username), 
                post.username, post.image, post.description, post.hearts, post.event_post, post.hearted);
    }
}

showOnlineUsers();
showFeed();