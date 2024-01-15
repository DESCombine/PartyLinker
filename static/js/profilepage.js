import { request_path } from "/static/js/config.js";
import { loadUserImage, loadEvent, addNewPost, addEventDescription, likePost, loadComments, showComments, loadPartecipations, showPartecipations } from "/static/js/utils.js";


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

async function showPostedPosts() {
    const posts = document.getElementById("posts");
    const photos = await loadPosts();
    //const events = await loadEvents();
    let photo_index = 0;
    //let event_index = 0;
    let photo = photos[photo_index];
    //let event = events[event_index];
    let photosDiv = document.getElementById("photos");
    if (photos.length == 0 /*&& events.length == 0*/) {
        posts.innerHTML = "No posts to show";
        console.log("No posts to show");
    } else {
        while (photo_index < photos.length /*&& event_index < events.length*/ && photo.event_post == 0) {
            //if (photo.date_posted > event.date_posted) {
            //addNewPost(posts, photo.photo_id, await loadUserImage(photo.poster), photo.poster, photo.photo, photo.description);
            photosDiv.getElementsByTagName('div').item(photo_index).getElementsByTagName('img').item(0).src = "/static/img/uploads/" + photo.image;
            /*let img = posts.getElementsByTagName('div').item(2);//.getElementsByTagName('img').item(0);
            console.log(img);
            img.src = "/static/img/uploads/" + photo.photo;*/
            photo_index++;
            photo = photos[photo_index];
            console.log(photo_index);
            /*} else {
                addNewPost(posts, event.event_id, await loadUserImage(event.organizer), event.name, event.image, event.description, event);
                event_index++;
                event = events[event_index];
                console.log(event_index);
            }*/
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