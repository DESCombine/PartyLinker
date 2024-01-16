import { addNewPost } from "./utils.js";
import { request_path } from "/static/js/config.js";
import { loadUserImage } from "/static/js/utils.js";

const postButton = document.getElementById("buttons").getElementsByTagName("div").item(0)
const eventButton = document.getElementById("buttons").getElementsByTagName("div").item(1);
const templatePost = document.importNode(document.getElementById("template-photos"), true);
const templateModal = document.importNode(document.getElementById("post-template"), true);
postButton.addEventListener("click", function(){ changeView("post"); });
eventButton.addEventListener("click", function(){ changeView("event"); });
postButton.style.pointerEvents = "none";


function changeView(button) {
    let type = 0;
    removeAll();
    if(button === "post") {
        postButton.style.pointerEvents = "none";
        eventButton.style.pointerEvents = "auto";
        type = 0;
    } else if(button === "event"){
        eventButton.style.pointerEvents = "none";
        postButton.style.pointerEvents = "auto";
        type = 1;
    }
    showPhotos(type)
}

function removeAll() {
    const photosDiv = document.getElementById("photos");
    while (photosDiv.firstElementChild != null) {
        photosDiv.removeChild(photosDiv.firstChild);
    }
    photosDiv.appendChild(templatePost);
}

function cleanModal() {
    const modal = document.getElementById("post-modal");
    while (modal.firstElementChild != null) {
        modal.removeChild(modal.firstChild);
    }
    modal.appendChild(templateModal);
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

function openModal(post) {
    cleanModal();
    addNewPost(document.getElementById("post-template"), document.getElementById("post-modal"), post.id, post.event_id, post.user_photo, 
            post.username, post.image, post.description, post.hearts, post.event, post.hearted);
}

async function getType(type) {
    const photos = await loadPosts();
    const returnarray = [];
    photos.forEach(element => {
        if(element.event_post == type) {
            returnarray.push(element);
        }
    });
    return returnarray;
}

async function showPhotos(type) {
    const posts = document.getElementById("posts");
    let photos = (await getType(type));
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
            clone.querySelector("#photo-id").addEventListener("click", function(){ openModal(photo); });
            photo = photos[photo_index];
            photosDiv.appendChild(clone);
            dim++;
        }
        let i = 0;
        if(dim % 3 == 1) {
            i = 2;
        } else if (dim % 3 == 2){
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
showPhotos(0);