import { request_path } from "/static/js/config.js?v=10";
import { loadUserImage, cleanTemplateList, showPhotos } from "/static/js/utils.js?v=10";

document.querySelector("#modifyIcon").href = "/modifyprofile/modifyprofile.html";
const postButton = document.getElementById("buttons").getElementsByTagName("div").item(0);
const eventButton = document.getElementById("buttons").getElementsByTagName("div").item(1);
const templatePost = document.importNode(document.getElementById("template-photos"), true);
postButton.addEventListener("click", function () { changeView("post"); });
eventButton.addEventListener("click", function () { changeView("event"); });
document.getElementById("comments-modal").addEventListener("hidden.bs.modal", 
        function() { cleanTemplateList(document.querySelector("#comments-modal ol")); });
postButton.style.pointerEvents = "none";

/**
 * Loads the posts published by the user
 * @param {String} user the owner of the posts
 * @returns the posts of the user
 */
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

/**
 * Loads the profile informations of the user
 * @param {*} user the owner of the profile
 * @returns the profile informations
 */
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

/**
 * Switches beetween the post and event view
 * @param {String} button the view to show
 */
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

/**
 * Gets the posts of the user of a certain type
 * @param {String} type the type of the post
 * @param {String} user the owner of the posts 
 * @returns the posts of the user of a certain type
 */
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

/**
 * Removes all the posts from the page
 */
function removeAll() {
    const photosDiv = document.getElementById("photos");
    while (photosDiv.firstElementChild != null) {
        photosDiv.removeChild(photosDiv.firstChild);
    }
    photosDiv.appendChild(templatePost);
}

/**
 * Shows the profile informations of the user
 * @param {*} user the owner of the profile
 */
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

/**
 * Checks if the user follows the owner of the profile
 */
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

// If the user is visiting his own profile, show the modify icon and the follow button
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

showProfileInfos(user);
showPhotos(await getPostType(0, user));