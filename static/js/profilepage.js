import { request_path } from "/static/js/config.js";
import { loadUserImage } from "/static/js/utils.js";

const postButton = document.getElementById("buttons").getElementsByTagName("div").item(0)
const eventButton = document.getElementById("buttons").getElementsByTagName("div").item(1);
postButton.addEventListener("click", changePosted);
eventButton.addEventListener("click", changeEvents);

let posted = true;
let events = false;

function changePosted() {
    posted = !posted;
    postButton.style.pointerEvents = "none";
    changeView();
    eventButton.style.pointerEvents = "auto";
}

function changeEvents() {
    eventButton.style.pointerEvents = "none";
    events = !events;
    postButton.style.pointerEvents = "auto";
}

function changeView() {
    removeAll();
    if(posted) {
        showPostedPosts();
    } else if (events) {
        showEvents();
    }
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

async function showPostedPosts() {
    const posts = document.getElementById("posts");
    const photos = await loadPosts();
    //const events = await loadEvents();
    let photo_index = 0;
    //let event_index = 0;
    let photo = photos[photo_index];
    //let event = events[event_index];
    let photosDiv = document.getElementById("photos");
    let template = document.getElementById("template-photos");
    console.log(template);
    if (photos.length == 0 /*&& events.length == 0*/) {
        posts.innerHTML = "No posts to show";
        console.log("No posts to show");
    } else {
        while (photo_index < photos.length /*&& event_index < events.length*/ && photo.event_post == 0) {
            //if (photo.date_posted > event.date_posted) {
            //addNewPost(posts, photo.photo_id, await loadUserImage(photo.poster), photo.poster, photo.photo, photo.description);
            let clone = document.importNode(template.content, true);
            clone.querySelector("#photo-id").src = "/static/img/uploads/" + photo.image;
            clone.querySelector("#photo-id").onclick = openModal;
            //clone.getElementsByTagName('div').item(photo_index).getElementsByTagName('img').item(0).src = "/static/img/uploads/" + photo.image;
            /*let img = posts.getElementsByTagName('div').item(2);//.getElementsByTagName('img').item(0);
            console.log(img);
            img.src = "/static/img/uploads/" + photo.photo;*/
            photo_index++;
            photo = photos[photo_index];
            console.log(photo_index);
            photosDiv.appendChild(clone);
            /*} else {
                addNewPost(posts, event.event_id, await loadUserImage(event.organizer), event.name, event.image, event.description, event);
                event_index++;
                event = events[event_index];
                console.log(event_index);
            }*/
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