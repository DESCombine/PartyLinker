import { request_path } from "/static/js/config.js?v=9";
import { loadUserImage, cleanTemplateList, resetEventListener, translatePost, showComments } from "/static/js/utils.js?v=9";

document.querySelector("#modifyIcon").href = "/modifyprofile/modifyprofile.html";

const postButton = document.getElementById("buttons").getElementsByTagName("div").item(0);
const eventButton = document.getElementById("buttons").getElementsByTagName("div").item(1);
const templatePost = document.importNode(document.getElementById("template-photos"), true);
postButton.addEventListener("click", function () { changeView("post"); });
eventButton.addEventListener("click", function () { changeView("event"); });
document.getElementById("comments-modal").addEventListener("hidden.bs.modal", 
function() { cleanTemplateList(document.querySelector("#comments-modal ol")); });
postButton.style.pointerEvents = "none";

async function loadPosts(user) {
    const request_url = user == null ? request_path + "/user/load_posted.php" : request_path + "/user/load_posted.php?user=" + user;
    const response = await fetch(request_url, {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    });
    const posts = await response.json();
    return posts;
}

async function loadProfileInfos(user) {
    const request_url = user == null ? request_path + "/user/load_profile_infos.php" : request_path + "/user/load_profile_infos.php?user=" + user;
    const response = await fetch(request_url, {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    });
    const infos = await response.json();
    return infos;
}

async function changeView(button) {
    let type = 0;
    removeAll();
    const postP = postButton.getElementsByTagName("p").item(0);
    const eventP = eventButton.getElementsByTagName("p").item(0);
    if (button === "post") {
        postButton.style.pointerEvents = "none";
        eventButton.style.pointerEvents = "auto";
        postP.classList.add("border");
        postP.classList.add("border-black");
        eventP.classList.remove("border");
        eventP.classList.remove("border-black");
        type = 0;
    } else if (button === "event") {
        eventButton.style.pointerEvents = "none";
        postButton.style.pointerEvents = "auto";
        eventP.classList.add("border");
        eventP.classList.add("border-black");
        postP.classList.remove("border");
        postP.classList.remove("border-black");
        type = 1;
    }
    showPhotos(await getPostType(type, user));
}

async function getPostType(type, user) {
    const photos = await loadPosts(user);
    const returnarray = [];
    photos.forEach(element => {
        if (element.event_post == type) {
            returnarray.push(element);
        }
    });
    return returnarray;
}

function removeAll() {
    const photosDiv = document.getElementById("photos");
    while (photosDiv.firstElementChild != null) {
        photosDiv.removeChild(photosDiv.firstChild);
    }
    photosDiv.appendChild(templatePost);
}

async function showProfileInfos(user) {
    const infos = await loadProfileInfos(user);
    document.getElementById("username").innerHTML = infos.username;
    document.getElementById("description").innerHTML = infos.bio;
    document.getElementById("followers").innerHTML = infos.followers;
    document.getElementById("followed").innerHTML = infos.followed;
    if (await loadUserImage(infos.username) == null) {
        document.getElementById("profileImage").src = "/static/img/default-profile.png";
    } else {
        document.getElementById("profileImage").src = "/static/img/uploads/" + await loadUserImage(infos.username);
    }
    if (infos.background != null) {
        document.getElementById("bannerImage").src = "/static/img/uploads/" + infos.background;
    } else {
        document.getElementById("bannerImage").src = "/static/img/default-poster.png";
    }
}

async function checkFollow() {
    const response = await fetch(request_path + "/user/check_if_follows.php?user=" + user, {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    });
    const result = await response.json();
    if (result.follows) {
        document.getElementById("followButton").innerHTML = '<em class="fa-solid fa-user-check"></em>';
    } else {
        document.getElementById("followButton").innerHTML = '<em class="fa-solid fa-user-plus"></em>';
    }
}

const user = new URLSearchParams(window.location.search).get("user");
if (user != null) {
    // In case the user is visiting his own profile, redirect to /profile
    loadProfileInfos(null).then((infos) => {
        if (infos.username == user) {
            window.location.replace("/profile/");
        }
    });
    document.getElementById("modifyIcon").classList.add("d-none");
    const followButton = document.getElementById("followButton");
    followButton.classList.remove("d-none");
    window.toggleFollow = async () => {
        const response = await fetch(request_path + "/user/toggle_follow.php?user=" + user, {
            method: "GET",
            headers: {
                "Content-Type": "application/json"
            },
            credentials: "include"
        });
        const result = await response.json();
        if (result.message == "success") {
            checkFollow();
            showProfileInfos(user);
        }
    }
    checkFollow();
    document.getElementById("mobileProfileNav").href = "/profile/";
    document.getElementById("mobileProfileNav").innerHTML = '<em class="fa-solid fa-user"></em>';
    document.getElementById("desktopProfileNav").href = "/profile/";
    document.getElementById("desktopProfileNav").innerHTML = '<em class="fa-solid fa-user"></em>';
}

function openModal(post) {
    showModalPost(post.post_id, post.event_id, post.username, post.image,
            post.description, post.likes, post.liked);
}

async function showModalPost(post_id, event_id, username,
        image, description, likes, liked) {
    const postActions = document.getElementById("post-actions");
    // clean buttons event listeners
    document.getElementById("translate").replaceWith(document.getElementById("translate").cloneNode(true));
    document.getElementById("comments-button").replaceWith(document.getElementById("comments-button").cloneNode(true));
    document.getElementById("likes-button").replaceWith(document.getElementById("likes-button").cloneNode(true));
    // show modal
    document.getElementById("post-user-photo").src = "/static/img/uploads/" + await loadUserImage(username);
    document.getElementById("post-name").innerHTML = username;
    document.getElementById("post-photo").src = "/static/img/uploads/" + image;
    document.getElementById("post-likes").innerHTML = likes;
    document.getElementById("post-description").innerHTML = description;
    document.getElementById("translate").addEventListener("click", function () { translatePost(post_id, document.getElementById("post-description")); });
    const likeButton = postActions.querySelector("#likes-button");
    if (liked) {
        likeButton.addEventListener("click", function () { removeLike(post_id, "post") });
        likeButton.innerHTML = "<em class='fa-solid fa-heart'></em>";
    } else {
        likeButton.addEventListener("click", function () { addLike(post_id, "post") });
    }
    postActions.querySelector("#comments-button").addEventListener("click", function() { showComments(post_id); });
    postActions.querySelector("a").href = "/event/eventpage.html?id=" + event_id;
}

function showPhotos(photos) {
    let photo = photos[0];
    let photosDiv = document.getElementById("photos");
    let template = document.getElementById("template-photos");
    if (photos.length == 0) {
        photosDiv.innerHTML = "No posts to show";
    } else {
        photosDiv.innerHTML = "";
        let dim = 0;
        for (let photo_index = 0; photo_index < photos.length; photo_index++) {
            photo = photos[photo_index];
            let clone = document.importNode(template.content, true);
            clone.querySelector("#photo-id").src = "/static/img/uploads/" + photo.image;
            clone.querySelector("#photo-id").addEventListener("click", function () { openModal(photos[photo_index]); });
            photosDiv.appendChild(clone);
            dim++;
        }
        let i = 0;
        if (dim % 3 == 1) {
            i = 2;
        } else if (dim % 3 == 2) {
            i = 1;
        }
        for (let j = 0; j < i; j++) {
            let clone = document.importNode(template.content, true);
            clone.querySelector("div").style.visibility = "hidden";
            clone.querySelector("div").classList.add("invisible");
            photosDiv.appendChild(clone);
        }
    }
}

async function addLike(like_id, type) {
    like(like_id, type, "/user/upload_like.php", 1);
}

async function removeLike(like_id, type) {
    like(like_id, type, "/user/remove_like.php", -1);
}

async function like(like_id, type, request, addOrRemove) {
    await fetch(request_path + request, {
        method: "POST",
        credentials: "include",
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            "like_id": like_id,
            "type": type
        })
    });
    const likes = document.getElementById("post-likes");
    const likeButton = document.getElementById("likes-button");
    likes.innerHTML = parseInt(likes.innerHTML) + addOrRemove;
    let fun;
    if (addOrRemove == 1) {
        likeButton.innerHTML = "<em class='fa-solid fa-heart'></em>";
        fun = function() { removeLike(like_id, type, likeButton, likes); };
    } else {
        likeButton.innerHTML = "<em class='fa-regular fa-heart'></em>";
        fun = function() { addLike(like_id, type, likeButton, likes); };
    }
    resetEventListener(likeButton, fun);
}

showProfileInfos(user);
showPhotos(await getPostType(0, user));


