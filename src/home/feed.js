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

async function loadPhotoComments(photo_index) {
    const response = await fetch("https://api.partylinker.live/load_comments_photo?photo=" + photos[photo_index].id);
    const comments = await response.json();
    return comments;
}

async function loadUserImage(user_id) {
    const response = await fetch("https://api.partylinker.live/load_user_image?user=" + user_id);
    const image = await response.json();
    return image;
}