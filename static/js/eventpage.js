import { request_path } from "/static/js/config.js?v=11";
import { showPhotos, loadEvent, showComments, like,
        translatePost, loadPartecipations, addEventDescription, cleanTemplateList } from "/static/js/utils.js?v=11";

// First the id is taken from the url, then the post and the event informations are loaded
const event_id = new URLSearchParams(window.location.search).get('id');
const post = await loadPostEvent(event_id);
const event = await loadEvent(event_id);

/**
 * Used to load the event informations while having the event id
 * @param {int} event_id 
 * @returns the event associated
 */
async function loadPostEvent(event_id) {
    const response = await fetch(request_path + "/user/load_post_event.php?event=" + event_id, {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    
    });
    const post_id = await response.json();
    return post_id;
}

/**
 * Loads the photos taken at the event
 * @returns the photos
 */
async function loadPhotos() {
    const response = await fetch(request_path + "/user/load_event_posts.php?event=" + event_id, {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    
    });
    const photos = await response.json();
    return photos;
}

/**
 * Shows the content of the event page:
 * the event poster, the photos and the partecipants
 */
async function showContent() {
    // poster
    const poster = document.getElementById("event-poster");
    poster.querySelector("img").src = "/static/img/uploads/" + post.image;
    poster.querySelector("#poster-likes").innerHTML = post.likes;
    const likeButton = document.getElementById("poster-like-bt");
    likeButton.addEventListener("click", function () { like(post.post_id, "post", !post.liked, document, "#poster-like-bt", "#poster-likes") });
    if (post.liked) {
        likeButton.innerHTML = "<em class='fa-solid fa-heart'></em>";
    }
    document.querySelector("#poster-comment-bt").addEventListener("click", function () { showComments(post.post_id); })
    let desc = poster.querySelector("#poster-description");
    desc.innerHTML = post.description;
    poster.querySelector("#poster-translate-bt").addEventListener("click", function () { translatePost(post.post_id, desc); });
    addEventDescription(poster.querySelector("#poster-info"), event);
    document.getElementById("comments-modal").addEventListener("hidden.bs.modal", 
            function() { cleanTemplateList(document.querySelector("#comments-modal ol")); });
    // photos
    showPhotos(await loadPhotos());

    // partecipants
    let partecipants = await loadPartecipations(event_id);
    let partecipantsDiv = document.getElementById("people");
    let template = document.getElementById("template-partecipants");
    if (partecipants.length == 0) {
        partecipantsDiv.innerHTML = "No partecipants to show";
    } else {
        for (let partecipant_index = 0; partecipant_index < partecipants.length; partecipant_index++) {
            let clone = document.importNode(template.content, true);
            clone.querySelector("img").src = "/static/img/uploads/" + partecipants[partecipant_index].profile_photo;
            clone.querySelector("a").href = "/profile?user=" + partecipants[partecipant_index].username;
            clone.querySelector("a").innerHTML = partecipants[partecipant_index].username;
            partecipantsDiv.appendChild(clone);
        }
    }
}

showContent();