/**
 *  This file is used to house every function that is used in multiple files
 */

import { request_path } from "/static/js/config.js?v=14";

/**
 * Cheks if the user doesn't have a token and redirects to the login page
 * @param {JSON} response the response from the server
 */
export function checkError(response) {
    if (response.error === "No token provided" || response.error === "Invalid token") {
        window.location.replace("/login/login.html");
    }
} 

/**
 * Loads the user's profile image
 * @param {int} user_id the user's id
 * @returns the user's profile image
 */
export async function loadUserImage(user_id) {
    const response = await fetch(request_path + "/user/load_user_img.php?user=" + user_id);
    const image = await response.json();
    return image;
}

/**
 * Loads an event from its id
 * @param {int} event_id the event's id
 * @returns the event
 */
export async function loadEvent(event_id) {
    const response = await fetch(request_path + "/user/load_event.php?event=" + event_id);
    const event = await response.json();
    return event;
}

/**
 * Checks if the user is an organizer
 * @returns true if the user is an organizer, false otherwise
 */
export async function checkOrganizer() {
    const response = await fetch(request_path + "/user/load_settings.php", {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    });
    const settings = await response.json();
    checkError(settings);
    return settings.organizer;
}

/**
 * Cleans a list of template elements
 * @param {Node} list the list to clean
 */
export function cleanTemplateList(list) {
    while (list.getElementsByTagName('li').length > 0) {
        list.removeChild(list.lastChild);
    }
}

/**
 * Removes and adds an event listener to a button
 * @param {Node} oldButton the button to replace
 * @param {Function} fun the function to add 
 * @returns a copy of the button with the new event listener
 */
export function resetEventListener(oldButton, fun) {
    const newButton = oldButton.cloneNode(true);
    oldButton.parentNode.replaceChild(newButton, oldButton);
    newButton.addEventListener("click", fun);
    return newButton;
}

/**
 * Adds a description to an event
 * @param {Node} parent the parent element
 * @param {JSON} event the event 
 */
export function addEventDescription(parent, event) {
    parent.querySelector("p").innerHTML = event.name;
    const infos = parent.querySelector("ol");
    infos.innerHTML = `
            <li class="list-group-item"><p>Place ${event.location}</p></li>
            <li class="list-group-item"><p>Starting Date: ${event.starting_date}</p></li>
            <li class="list-group-item"><p>Ending Date: ${event.ending_date}</p></li>
            <li class="list-group-item"><p>Vips: ${event.vips}</p></li>
            <li class="list-group-item"><p>Max-People: ${event.max_capacity}</p></li>
            <li class="list-group-item"><p>Price: ${event.price}</p></li>
            <li class="list-group-item"><p>Minimum age: ${event.minimum_age}</p></li>`;
}

/**
 * Loads the comments of a post
 * @param {int} post_id the id of the post
 * @returns the comments of the post
 */
async function loadComments(post_id) {
    const response = await fetch(request_path + "/user/load_comments.php?post=" + post_id, {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    });
    const comments = await response.json();
    return comments;
}

/**
 * Adds a comment to a post
 * @param {int} post_id the id of the post
 */
async function submitComment(post_id) {
    const modalFooter = document.querySelector("#comments-modal .modal-footer");
    const content = modalFooter.querySelector("input").value;
    modalFooter.querySelector("input").value = "";
    await fetch(request_path + "/user/upload_comment.php", {
        method: "POST",
        credentials: "include",
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            "post_id": post_id,
            "content": content
        })
    });
    cleanTemplateList(document.querySelector("#comments-modal ol"));
    showComments(post_id);
}

/**
 * Removes a comment from a post
 * @param {int} comment_id the id of the comment
 * @param {int} post_id the id of the post
 */
async function removeComment(comment_id, post_id) {
    await fetch(request_path + "/user/remove_comment.php", {
        method: "POST",
        credentials: "include",
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            "comment_id": comment_id
        })
    });
    cleanTemplateList(document.querySelector("#comments-modal ol"));
    showComments(post_id);
}

/**
 * Shows the comments of a post
 * @param {int} post_id the id of the post
 */
export async function showComments(post_id) {
    const comModal = document.querySelector("#comments-modal");
    const comments = comModal.querySelector("ol");
    const comments_to_show = await loadComments(post_id);
    const template = comModal.querySelector("template");
    for (let i = 0; i < comments_to_show.length; i++) {
        let comment = comments_to_show[i];
        let clone = template.content.cloneNode(true);
        clone.querySelector("li").setAttribute("name", "comment" + comment.comment_id);
        let profile_photo = comment.profile_photo == null ? "/static/img/default-profile.png" : "/static/img/uploads/" + comment.profile_photo;
        clone.querySelector("img").src = profile_photo;
        clone.querySelector("a").textContent = comment.username;
        clone.querySelector("a").href = "/profile?user=" + comment.username;
        clone.querySelector(".content").textContent = comment.content;
        if (comment.owner) {
            clone.querySelector(".trash-button").classList.remove("invisible");
            clone.querySelector(".trash-button").addEventListener("click", function() { removeComment(comment.comment_id, post_id); })
        }
        clone.querySelector(".likes").innerHTML = comment.likes;
        const likeButton = clone.querySelector(".like-button");
        likeButton.addEventListener("click", function () { templateLike(comment.comment_id, "comment", !comment.liked)});
        if (comment.liked) {
            likeButton.innerHTML = "<em class='fa-solid fa-heart'></em>";
        }
        comments.appendChild(clone);
    }
    const modalFooter = comModal.querySelector(".modal-footer");
    modalFooter.querySelector("form").onkeydown = function(event) {
        return event.key != 'Enter';
    }
    const comment_button = modalFooter.querySelector("button");
    resetEventListener(comment_button, function() { submitComment(post_id); });
}

/**
 * Loads the partecipants of an event
 * @param {int} event_id the id of the event
 * @returns the partecipants of the event
 */
export async function loadPartecipations(event_id) {
    const response = await fetch(request_path + "/user/load_partecipations.php?event=" + event_id, {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    });
    const partecipations = await response.json();
    return partecipations;
}

/**
 * Reacts to a partecipate/busy button click
 * @param {int} event_id the id of the event
 * @param {String} request the request to send to the server 
 */
async function partecipationClick(event_id, request) {
    await fetch(request_path + request, {
        method: "POST",
        credentials: "include",
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            "event_id": event_id
        })
    });
    const partList = document.querySelector("#partecipants-modal ul");
    cleanTemplateList(partList);
    showPartecipations(event_id);
}

/**
 * Shows the partecipants of an event
 * @param {int} event_id the id of the event
 */
export async function showPartecipations(event_id) {
    const partModal = document.querySelector("#partecipants-modal")
    const partecipations = partModal.querySelector("ul");
    const partecipations_list = await loadPartecipations(event_id);
    let template = partecipations.querySelector("template");
    let isUserPartecipating = false;
    for (let i = 0; i < partecipations_list.length; i++) {
        let partecipation = partecipations_list[i];
        if (partecipation.partecipating) {
            isUserPartecipating = true;
        }
        let clone = template.content.cloneNode(true);
        let profile_photo = partecipation.profile_photo == null ? "/static/img/default-profile.png" : "/static/img/uploads/" + partecipation.profile_photo;
        clone.querySelector("img").src = profile_photo;
        clone.querySelector("a").textContent = partecipation.username;
        clone.querySelector("a").href = "/profile?user=" + partecipation.username;
        partecipations.appendChild(clone);
    }
    const partecipants_button = partModal.getElementsByTagName("button")[1];
    const busy_button = partModal.getElementsByTagName("button")[2];
    resetEventListener(partecipants_button, function() { 
            partecipationClick(event_id, "/user/upload_partecipation.php"); }).disabled = isUserPartecipating;
    resetEventListener(busy_button, function() { 
            partecipationClick(event_id, "/user/remove_partecipation.php"); }).disabled = !isUserPartecipating;
}

/**
 * Handles a like button click on a post or comment template
 * @param {int} like_id the id of the post or comment
 * @param {String} type the type of the like
 * @param {Boolean} addOrRemove true if the like is to be added, false if it is to be removed
 */
export function templateLike(like_id, type, addOrRemove) {
    const parent = document.getElementsByName(type+like_id)[0];
    like(like_id, type, addOrRemove, parent, ".like-button", ".likes");
}

/**
 * Removes or adds a like to a post or comment
 * @param {int} like_id the id of the post or comment
 * @param {String} type the type of the like
 * @param {int} addOrRemove true if the like is to be added, false if it is to be removed
 * @param {Node} parent the parent element of like buttons and likes
 * @param {String} likeButton_id the id of the like button element
 * @param {String} likes_id the id of the likes element
 */
export async function like(like_id, type, addOrRemove, parent, likeButton_id, likes_id) {
    const request = addOrRemove ? "/user/upload_like.php" : "/user/remove_like.php";
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
    const likeButton = parent.querySelector(likeButton_id);
    const likes = parent.querySelector(likes_id);
    if (addOrRemove) {
        likes.innerHTML = parseInt(likes.innerHTML) + 1;
        likeButton.innerHTML = "<em class='fa-solid fa-heart'></em>";
    } else {
        likes.innerHTML = parseInt(likes.innerHTML) - 1;
        likeButton.innerHTML = "<em class='fa-regular fa-heart'></em>";
    }
    const fun = function() { like(like_id, type, !addOrRemove, parent, likeButton_id, likes_id); };
    resetEventListener(likeButton, fun);
}

/**
 * Translates the description of a post
 * @param {int} post_id the id of the post
 * @param {Node} textElement the element to translate
 */
export async function translatePost(post_id, textElement) {
    const response = await fetch(request_path + "/user/retrieve_translate_datas.php", {
        method: "POST",
        credentials: "include",
        headers: {
            "Content-Type": "application/json"
        },
        body: JSON.stringify({
            "post_id": post_id,
        })
    });
    const data = await response.json();
    console.log(data);

    const url = 'https://google-translate113.p.rapidapi.com/api/v1/translator/text';
    const options = {
        method: 'POST',
        headers: {
            'content-type': 'application/x-www-form-urlencoded',
            'X-RapidAPI-Key': '723f176375mshdd99d11a5d9657cp1701adjsnbc2737f56f7c',
            'X-RapidAPI-Host': 'google-translate113.p.rapidapi.com'
        },
        body: new URLSearchParams({
            from: 'auto',
            to: data.language,
            text: data.description
        })
    };

    try {
        const response = await fetch(url, options);
        const result = await response.json();
        console.log(result);
        textElement.innerHTML = result.trans;
    } catch (error) {
        console.error(error);
    }
}

/**
 * Shows the modal with the post informations
 * @param {JSON} post the post to show
 */
async function showModalPost(post) {
    const postActions = document.getElementById("post-actions");
    // clean buttons event listeners
    document.getElementById("translate").replaceWith(document.getElementById("translate").cloneNode(true));
    document.getElementById("comments-button").replaceWith(document.getElementById("comments-button").cloneNode(true));
    document.getElementById("likes-button").replaceWith(document.getElementById("likes-button").cloneNode(true));
    // show modal
    document.getElementById("post-user-photo").src = "/static/img/uploads/" + await loadUserImage(post.username);
    document.getElementById("post-name").innerHTML = post.username;
    document.getElementById("post-photo").src = "/static/img/uploads/" + post.image;
    document.getElementById("post-likes").innerHTML = post.likes;
    document.getElementById("post-description").innerHTML = post.description;
    document.getElementById("translate").addEventListener("click", function () { translatePost(post.post_id, document.getElementById("post-description")); });
    const likeButton = postActions.querySelector("#likes-button");
    likeButton.addEventListener("click", function () { like(post.post_id, "post", !post.liked, document, "#likes-button", "#post-likes") });
    if (post.liked) {
        likeButton.innerHTML = "<em class='fa-solid fa-heart'></em>";
    }
    postActions.querySelector("#comments-button").addEventListener("click", function() { showComments(post.post_id); });
    postActions.querySelector("a").href = "/event/eventpage.html?id=" + post.event_id;
}

/**
 * Used to show the photos taken at the event 
 * @param {JSON} photos the photos to show
 */
export function showPhotos(photos) {
    let photo = photos[0];
    let photosDiv = document.getElementById("photos");
    let template = document.getElementById("template-photos");
    if (photos.length != 0) {
        let dim = 0;
        for (let photo_index = 0; photo_index < photos.length; photo_index++) {
            photo = photos[photo_index];
            let clone = document.importNode(template.content, true);
            clone.querySelector("img").src = "/static/img/uploads/" + photo.image;
            clone.querySelector("img").addEventListener("click", function () { showModalPost(photos[photo_index]); });
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

/**
 * Confirms that the user is online
 */
async function confirmOnline() {
    const res = await fetch(request_path + "/user/update_online.php", {
        method: "POST",
        credentials: "include"
    });
    const response = await res.json();
    checkError(response);
}

confirmOnline();
setInterval(confirmOnline, 5 * 60 * 1000);