async function loadPhotos() {
    // !TODO: Load the auth token from cookies
    const user_auth = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6ImRhbmlsby5tYWdsaWEifQ.ygTbgkYa-T0pt-PWvklf9eszCDxIudhjyNPN5m3npmo"
    const response = await fetch("https://api.partylinker.live/load_feed_photos", {
        method: "GET",
        headers: {
            "Authorization": "Bearer " + user_auth,
            "Content-Type": "application/json"
        }
    });
    const photos = await response.json();
    return photos;
}

async function loadEvents() {
    // !TODO: Load the auth token from cookies
    const user_auth = "eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJ1c2VybmFtZSI6ImRhbmlsby5tYWdsaWEifQ.ygTbgkYa-T0pt-PWvklf9eszCDxIudhjyNPN5m3npmo"
    const response = await fetch("https://api.partylinker.live/load_feed_posts", {
        method: "GET",
        headers: {
            "Authorization": "Bearer " + user_auth,
            "Content-Type": "application/json"
        }
    });
    const events = await response.json();
    return events;
}

async function getPostedPosts() {
    const posts = document.getElementById("posts");
    const photos = await loadPhotos();
    const events = await loadEvents();
    
}

async function showFeed() {
    const feed = document.getElementById("feed");
    const photos = await loadPhotos();
    const events = await loadEvents();
    let photo_index = 0;
    let event_index = 0;
    let photo = photos[photo_index];
    let event = events[event_index];
    while (photo_index < photos.length && event_index < events.length) {
        if (photo.date_posted > event.date_posted) {
            addNewPost(feed, photo.photo_id, await loadUserImage(photo.poster), photo.poster, photo.photo, photo.description);
            photo_index++;
            photo = photos[photo_index];
        } else {
            addNewPost(feed, event.event_id, await loadUserImage(event.organizer), event.name, event.image, event.description, event);
            event_index++;
            event = events[event_index];
        }
    }
}