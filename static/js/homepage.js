async function loadOnlineUsers() {
    const user_auth = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6ImRhbmlsby5tYWdsaWEifQ.ygTbgkYa-T0pt-PWvklf9eszCDxIudhjyNPN5m3npmo"
    const response = await fetch("https://api.partylinker.live/user/load_online_users.php", {
        method: "GET",
        headers: {
            "Authorization": "Bearer " + user_auth,
            "Content-Type": "application/json"
        }
    });
    const users = await response.json();
    return users;
}

async function showOnlineUsers() {
    const online_users = document.getElementById("online-users");
    const users = await loadOnlineUsers();
    let template = document.getElementById("online-user-template");
    let clone = document.importNode(template.content, true);
    for (let i = 0; i < users.length; i++) {
        let user = users[i];
        clone.querySelector("#online-image").src = await loadUserImage(user);
        clone.querySelector("#online-image").alt = user;
        online_users.appendChild(clone);
    }
}

async function loadPosts() {
    // !TODO: Load the auth token from cookies
    const user_auth = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6ImRhbmlsby5tYWdsaWEifQ.ygTbgkYa-T0pt-PWvklf9eszCDxIudhjyNPN5m3npmo"
    const response = await fetch("https://api.partylinker.live/user/load_feed.php", {
        method: "GET",
        headers: {
            "Authorization": "Bearer " + user_auth,
            "Content-Type": "application/json"
        }
    });
    const posts = await response.json();
    return posts;
}

async function loadUserImage(user_id) {
    const response = await fetch("https://api.partylinker.live/user/load_user_image.php?user=" + user_id);
    const image = await response.json();
    return image;
}

async function loadEvent(event_id) {
    const response = await fetch("https://api.partylinker.live/user/load_event.php?event=" + event_id);
    const event = await response.json();
    return event;
}

async function showFeed() {
    const feed = document.getElementById("feed");
    const posts = await loadPosts();
    let post_index = 0;
    while (post_index < posts.length) {
        let post = posts[post_index];
        addNewPost(feed, post.post_id, await loadUserImage(post.username), post.username, post.image, 
                post.description, post.likes, post.event_post);
        post_index++;
    }
}

async function addNewPost(feed, post_id, user_photo, username, image, description, likes, event) {
    let template = document.getElementById("post-template");
    let clone = document.importNode(template.content, true);
    clone.querySelector("#post-user-photo").src = user_photo;
    clone.querySelector("#post-name").innerHTML = username;
    clone.querySelector("#post-photo").src = image;
    clone.querySelector("#likes").innerHTML = likes;
    clone.querySelector("#comments-button").onclick = "showComments(" + post_id + ")";
    clone.querySelector("#partecipations-button").onclick = "showPartecipations(" + post_id + ")";
    clone.querySelector("#post-description").innerHTML = description;
    if (event) {
        addEventDescription(clone, await loadEvent(post.event_id));
    }
    feed.appendChild(clone);
}

function addEventDescription(post, event) {
    let template = document.getElementById("description-template");
    let clone = document.importNode(template.content, true);
    clone.querySelector("#event-name").innerHTML = event.name;
    clone.querySelector("#event-place").innerHTML = event.location;
    clone.querySelector("#event-date").innerHTML = event.starting_date + " - " + event.ending_date;
    clone.querySelector("#event-vips").innerHTML = event.vip;
    clone.querySelector("#event-people").innerHTML = event.max_people;
    clone.querySelector("#event-price").innerHTML = event.price;
    clone.querySelector("#event-age").innerHTML = event.minimum_age;
    post.appendChild(clone);
}

async function loadComments(post_id) {
    const response = await fetch("https://api.partylinker.live/user/load_comments.php?post=" + post_id);
    const comments = await response.json();
    return comments;
}

async function showComments(post_id) {
    const comments = document.getElementById("comments");
    const comments_to_show = await loadComments(post_id);
    let template = document.getElementById("comment-template");
    let clone = document.importNode(template.content, true);
    for (let i = 0; i < comments_to_show.length; i++) {
        let comment = comments_to_show[i];
        clone.querySelector("#comment-user-photo").src = await loadUserImage(comment.username);
        clone.querySelector("#comment-name").textContent = comment.username;
        clone.querySelector("#comment-content").textContent = comment.content;
        clone.querySelector("#comment-likes").innerHTML = comment.likes;
        comments.appendChild(clone);
    }
}

async function loadPartecipations(event_id) {
    const response = await fetch("https://api.partylinker.live/user/load_partecipations.php?event=" + event_id);
    const partecipations = await response.json();
    return partecipations;
}

async function showPartecipations(event_id) {
    const partecipations = document.getElementById("partecipations");
    const partecipations_list = await loadPartecipations(event_id);
    let template = document.getElementById("partecipation-template");
    let clone = document.importNode(template.content, true);
    for (let i = 0; i < partecipations_list.length; i++) {
        let partecipation = partecipations_list[i];
        clone.querySelector("#partecipation-user-photo").src = await loadUserImage(partecipation.partecipant);
        clone.querySelector("#partecipation-name").textContent = partecipation.partecipant;
        partecipations.appendChild(clone);
    }
}

showOnlineUsers();
showFeed();