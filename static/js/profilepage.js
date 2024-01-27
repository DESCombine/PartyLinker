import { request_path } from "/static/js/config.js?v=2";
import { loadUserImage, addLike, showComments, showPartecipations } from "/static/js/utils.js";

document.querySelector("#modifyIcon").href = "/modifyprofile/modifyprofile.html";

const postButton = document.getElementById("buttons").getElementsByTagName("div").item(0);
const eventButton = document.getElementById("buttons").getElementsByTagName("div").item(1);
const templatePost = document.importNode(document.getElementById("template-photos"), true);
postButton.addEventListener("click", function () { changeView("post"); });
eventButton.addEventListener("click", function () { changeView("event"); });
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

function changeView(button) {
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
    showPhotos(type);
}

async function getType(type, user) {
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

function openModal(post) {
    //console.log(post);
    const modal = document.getElementById("post-modal");
    showModalPost(modal, post.id, post.event_id, post.user_photo, post.username, post.image, post.description, post.likes, post.event_post);
}

async function showPhotos(type, user) {
    const posts = document.getElementById("posts");
    let photos = (await getType(type, user));
    let photo = photos[0];
    let photosDiv = document.getElementById("photos");
    let template = document.getElementById("template-photos");
    if (photos.length == 0) {
        posts.innerHTML = "No posts to show";
        console.log("No posts to show");
    } else {
        let dim = 0;
        for (let photo_index = 0; photo_index < photos.length; photo_index++) {
            let clone = document.importNode(template.content, true);
            clone.querySelector("#photo-id").src = "/static/img/uploads/" + photo.image;
            clone.querySelector("#photo-id").addEventListener("click", function () { openModal(photo); });
            photo = photos[photo_index];
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
            clone.querySelector("#photo-id").src = "/static/img/default-image.png";
            clone.querySelector("#photo-id").style.visibility = "hidden";
            photosDiv.appendChild(clone);
        }
    }
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

async function showModalPost(modal, post_id, event_id, user_photo, username,
    image, description, likes, event) {
    document.getElementById("post-user-photo").src = "/static/img/uploads/" + await loadUserImage(username);
    document.getElementById("post-name").innerHTML = username;
    document.getElementById("post-photo").src = "/static/img/uploads/" + image;
    document.getElementById("likes-button").addEventListener("click", function () { addLike(post_id, 1); });
    document.getElementById("comments-button").addEventListener("click", function () { showComments(post_id); })
    document.getElementById("post-likes").innerHTML = likes;
    document.getElementById("post-description").innerHTML = description;
    if (event) {
        document.getElementById("details-button").addEventListener("click", function () { window.location.replace("/event/eventpage.html?id=" + event_id); });
        document.getElementById("details-button").classList.remove("invisible");
        document.getElementById("partecipants-button").addEventListener("click", function () { showPartecipations(event_id); })
        document.getElementById("partecipants-button").addEventListener("click", function () { showPartecipations(event_id); });
        document.getElementById("partecipants-button").classList.remove("invisible");
    }
    document.getElementById("translate").addEventListener("click", function () { translatePost(post_id); });
}

async function translatePost(post_id) {
    const response = await fetch(request_path + "/user/retrieve_translate_datas.php", {
        method: "POST",
        credentials: "include",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            "post_id": post_id
        })
    });
    const description = await response.json();
    console.log(description);
    document.getElementById("post-description").innerHTML = translated;
    const axios = require('axios');

    const options = {
        method: 'POST',
        url: 'https://microsoft-translator-text.p.rapidapi.com/BreakSentence',
        params: {
            'api-version': '3.0'
        },
        headers: {
            'content-type': 'application/json',
            'X-RapidAPI-Key': '723f176375mshdd99d11a5d9657cp1701adjsnbc2737f56f7c',
            'X-RapidAPI-Host': 'microsoft-translator-text.p.rapidapi.com'
        },
        data: [
            {
                Text: description
            }
        ]
    };

    try {
        const response = await axios.request(options);
        console.log(response.data);
    } catch (error) {
        console.error(error);
    }
}

const user = new URLSearchParams(window.location.search).get("user");
console.log(user);
showProfileInfos(user);
showPhotos(0, user);
if (user != null) {
    document.getElementById("modifyIcon").classList.add("d-none");
}
