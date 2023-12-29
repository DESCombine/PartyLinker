async function loadPhotoComments(photo_index) {
    const response = await fetch("https://api.partylinker.live/load_comments_photo?photo=" + photos[photo_index].id);
    const comments = await response.json();
    return comments;
}

async function loadEventComments(event_index) {
    const response = await fetch("https://api.partylinker.live/load_comments_event?event=" + photos[event_index].id);
    const comments = await response.json();
    return comments;
}