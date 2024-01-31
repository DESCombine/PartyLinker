import { request_path } from "/static/js/config.js?v=2";
import { checkError, cleanTemplateList, removeLike, addLike, addEventDescription, loadEvent, showComments, showPartecipations } from "/static/js/utils.js?v=1";

async function loadOnlineUsers() {
    const response = await fetch(request_path + "/user/load_online_users.php", {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    });
    const users = await response.json();    
    checkError(users);
    return users;
}

async function showOnlineUsers() {
    const online_users = document.querySelector("#online-users");
    const users = await loadOnlineUsers();
    const template = online_users.querySelector("template");
    for (let i = 0; i < users.length; i++) {
        let user = users[i];
        let clone = template.content.cloneNode(true);
        let profile_photo = user.profile_photo == null ? "/static/img/default_profile.png" : "/static/img/uploads/" + user.profile_photo;
        clone.querySelector("img").src = profile_photo;
        clone.querySelector("img").alt = user.username;
        online_users.appendChild(clone);
    }
    if (users.length == 0) {
        online_users.innerHTML = "<p class='fs-5 mt-3'>No users online.</p>";
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
    checkError(posts);
    return posts;
}

async function showFeed() {
    const feed = document.querySelector("#feed");
    const posts = await loadFeed();
    const template = feed.querySelector("template");
    for (let i = 0; i < posts.length; i++) {
        let post = posts[i];
        let clone = template.content.cloneNode(true);
        addNewFeedPost(clone, feed, post.post_id, post.event_id, post.profile_photo, 
                post.username, post.image, post.description, post.likes, post.event_post, post.liked);
    }
    document.getElementById("comments-modal").addEventListener("hidden.bs.modal", 
            function() { cleanTemplateList(document.querySelector("#comments-modal ol")); });
    document.getElementById("partecipants-modal").addEventListener("hidden.bs.modal", 
            function() { cleanTemplateList(document.querySelector("#partecipants-modal ul")); });
    if (posts.length == 0) {
        feed.parentNode.innerHTML = "<p class='fs-4 text-center'>No posts to show.\
                Start following new users with the search page <i class='fa-solid fa-magnifying-glass'></i></p>";
    }
}

async function addNewFeedPost(clone, feed, post_id, event_id, user_photo, username,
        image, description, likes, event, liked) {
    const postUser = clone.querySelector(".post-user");
    const postContent = clone.querySelector(".post-content");
    const postActions = postContent.querySelector("ol");

    clone.querySelector("li").setAttribute("name", "post" + post_id);
    let profile_photo = postUser.querySelector("img") == null ? "/static/img/default_profile.png" : "/static/img/uploads/" + user_photo;
    postUser.querySelector("img").src = profile_photo;
    postUser.querySelector("a").innerHTML = username;
    postUser.querySelector("a").href = "/profile?user=" + username;
    postContent.querySelector("img").src = "/static/img/uploads/" + image;
    postActions.querySelector(".likes").innerHTML = likes;

    const likeButton = postActions.querySelector(".like-button");
    if (liked) {
        likeButton.addEventListener("click", function() { removeLike(post_id, 'post'); });
        likeButton.innerHTML = "<i class='fa-solid fa-heart text-danger'></i>";
    } else {
        likeButton.addEventListener("click", function() { addLike(post_id, 'post'); });
    }
    postActions.querySelector(".comment-button").addEventListener("click", function() { showComments(post_id); });
    postContent.querySelector(".post-description").innerHTML = description;
    
    const eventInfo = postContent.querySelector(".event-info");
    if (event) {
        postActions.querySelector(".partecipate-button").addEventListener("click", function() { showPartecipations(event_id); });
        postActions.querySelector(".partecipate-button").classList.remove("invisible");
        addEventDescription(eventInfo, await loadEvent(event_id));
    } else {
        eventInfo.innerHTML = "";
        postActions.querySelector("a").href = "/event/eventpage.html?id=" + event_id;
        postActions.querySelector("a").classList.remove("invisible");
        postActions.insertBefore(postActions.lastElementChild, postActions.lastElementChild.previousElementSibling);
    }
    feed.appendChild(clone);
}

showOnlineUsers();
showFeed();