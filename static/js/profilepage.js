import { request_path } from "/static/js/config.js";
import { loadUserImage } from "/static/js/utils.js";

const postButton = document.getElementById("buttons").getElementsByTagName("div").item(0)
const eventButton = document.getElementById("buttons").getElementsByTagName("div").item(1);
const template = document.importNode(document.getElementById("template-photos"), true);
postButton.addEventListener("click", function(){ changeView("post"); });
eventButton.addEventListener("click", function(){ changeView("event"); });
postButton.style.pointerEvents = "none";


function changeView(button) {
    removeAll();
    if(button === "post") {
        postButton.style.pointerEvents = "none";
        eventButton.style.pointerEvents = "auto";
        showPostedPosts();
    } else if(button === "event"){
        eventButton.style.pointerEvents = "none";
        postButton.style.pointerEvents = "auto";
        showEvents();
    }
}

function removeAll() {
    console.log(template);
    const photosDiv = document.getElementById("photos");
    while (photosDiv.firstElementChild != null) {
        photosDiv.removeChild(photosDiv.firstChild);
    }
    photosDiv.appendChild(template);
}

async function loadPosts() {
    const response = await fetch(request_path + "/user/load_posted.php", {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    });
    const posts = await response.json();
    return posts;
}

async function loadProfileInfos() {
    const response = await fetch(request_path + "/user/load_profile_infos.php", {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    });
    const infos = await response.json();
    return infos;
}

function openModal() {
    alert("Modal opened");
}

async function showEvents() {
    
}

async function showPostedPosts() {
    const posts = document.getElementById("posts");
    const photos = await loadPosts();
    let photo_index = 0;
    let photo = photos[photo_index];
    let photosDiv = document.getElementById("photos");
    let template = document.getElementById("template-photos");
    if (photos.length == 0) {
        posts.innerHTML = "No posts to show";
        console.log("No posts to show");
    } else {
        while (photo_index < photos.length && photo.event_post == 0) {
            let clone = document.importNode(template.content, true);
            clone.querySelector("#photo-id").src = "/static/img/uploads/" + photo.image;
            clone.querySelector("#photo-id").onclick = openModal;
            photo_index++;
            photo = photos[photo_index];
            photosDiv.appendChild(clone);
        }
        let i = 0;
        if(photos.length % 3 == 1) {
            i = 2;
        } else if (photos.length % 3 == 2){
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

async function showProfileInfos() {
    const infos = await loadProfileInfos();
    document.getElementById("username").innerHTML = infos.username;
    document.getElementById("description").innerHTML = infos.bio;
    document.getElementById("followers").innerHTML = infos.followers;
    document.getElementById("followed").innerHTML = infos.followed;
    document.getElementById("profileImage").src = "/static/img/uploads/" + await loadUserImage(infos.username);
    //document.getElementById("bannerImage").src = "/static/img/uploads/" + infos.banner;
}

showProfileInfos();
showPostedPosts();