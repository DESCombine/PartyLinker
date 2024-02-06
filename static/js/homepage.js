import { request_path } from "/static/js/config.js?v=10";
import { checkError, cleanTemplateList, templateLike, 
        addEventDescription, loadEvent, showComments, showPartecipations, translatePost } from "/static/js/utils.js?v=10";

/**
 * Loads the followed users currently online
 * @returns the online users
*/
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

/**
 * Shows the online users in the homepage
 */
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
        clone.querySelector("a").href = "/profile?user=" + user.username;
        clone.querySelector("a").innerHTML = user.username;
        online_users.appendChild(clone);
    }
    if (users.length == 0) {
        online_users.innerHTML = "<p class='fs-5 mt-3'>No users online.</p>";
    }
}

/**
 * Loads the most recent posts in the followed feed
 * @returns the posts in the feed
 */
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

/**
 * Shows the feed in the homepage
 */
async function showFeed() {
    const feed = document.querySelector("#feed");
    const posts = await loadFeed();
    const template = feed.querySelector("template");
    for (let i = 0; i < posts.length; i++) {
        let post = posts[i];
        let clone = template.content.cloneNode(true);
        addNewFeedPost(clone, feed, post);
    }
    document.getElementById("comments-modal").addEventListener("hidden.bs.modal", 
            function() { cleanTemplateList(document.querySelector("#comments-modal ol")); });
    document.getElementById("partecipants-modal").addEventListener("hidden.bs.modal", 
            function() { cleanTemplateList(document.querySelector("#partecipants-modal ul")); });
    if (posts.length == 0) {
        feed.parentNode.innerHTML = "<p class='fs-4 text-center mt-5'>No posts to show.\
                Start following new users with the search page <em class='fa-solid fa-magnifying-glass'></em></p>";
    }
}

/**
 * Adds a new post to the feed
 * @param {Node} clone the parent node of the post template 
 * @param {Element} feed the element that contains the posts
 * @param {JSON} post the post to add
 */
async function addNewFeedPost(clone, feed, post) {
    const postUser = clone.querySelector(".post-user");
    const postContent = clone.querySelector(".post-content");
    const postActions = postContent.querySelector("ol");

    clone.querySelector("li").setAttribute("name", "post" + post.post_id);
    let profile_photo = post.profile_photo == null ? "/static/img/default-profile.png" : "/static/img/uploads/" + post.profile_photo;
    postUser.querySelector("img").src = profile_photo;
    postUser.querySelector("a").innerHTML = post.username;
    postUser.querySelector("a").href = "/profile?user=" + post.username;
    postContent.querySelector("img").src = "/static/img/uploads/" + post.image;
    postActions.querySelector(".likes").innerHTML = post.likes;
    const likeButton = postActions.querySelector(".like-button");
    likeButton.addEventListener("click", function () { templateLike(post.post_id, "post", !post.liked) });
    if (post.liked) {
        likeButton.innerHTML = "<em class='fa-solid fa-heart'></em>";
    }

    postActions.querySelector(".comment-button").addEventListener("click", function() { showComments(post.post_id); });
    let desc = postContent.querySelector(".post-description");
    desc.innerHTML = post.description;
    postContent.querySelector(".translate-button").addEventListener("click", function () { translatePost(post.post_id, desc); });
    
    const eventInfo = postContent.querySelector(".event-info");
    if (post.event_post) {
        postActions.querySelector(".partecipate-button").addEventListener("click", function() { showPartecipations(post.event_id); });
        postActions.querySelector(".partecipate-button").classList.remove("invisible");
        addEventDescription(eventInfo, await loadEvent(post.event_id));
    } else {
        eventInfo.innerHTML = "";
        postActions.querySelector("a").href = "/event/eventpage.html?id=" + post.event_id;
        postActions.querySelector("a").classList.remove("invisible");
        postActions.insertBefore(postActions.lastElementChild, postActions.lastElementChild.previousElementSibling);
    }
    feed.appendChild(clone);
}

showOnlineUsers();
showFeed();