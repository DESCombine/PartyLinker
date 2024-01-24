import { request_path } from "/static/js/config.js?v=1";
import { checkOrganizer } from "/static/js/utils.js";

document.getElementById("upload-modal").addEventListener("shown.bs.modal", function() { renderFooter() });

async function renderFooter() {
    if (checkOrganizer()) {
        document.getElementById("new-event").classList.remove("invisible");
        document.getElementById("new-event").addEventListener("click", function() { selectEvent(0); });
    } else {
        document.getElementById("upload-modal")
                .getElementsByClassName("modal-footer")[0].innerHTML = "<p class='text-center'>Had Fun?&#129321</p>";
    }
}

const searchbar = document.getElementById("upload-searchbar");
searchbar.addEventListener("keyup", function(event) {
    if (event.key === 'Enter') {
        showSearchResults(searchbar.value);
    }
});

async function search(event) {
    const response = await fetch(request_path + "/user/search_event.php?event=" + event);
    const events = await response.json();
    return events;
}

async function showSearchResults(event) {
    const searchResults = document.getElementById("search-event-results");
    const events = await search(event);
    const template = document.getElementById("event-result-template");
    for (let i = 0; i < events.length; i++) {
        const event = events[i];
        let clone = document.importNode(template.content, true);
        clone.querySelector("#ev-res-id").setAttribute("name", "event"+event.event_id);
        clone.querySelector("#ev-res-image").src = "/static/img/uploads/" + event.image;
        clone.querySelector("#ev-res-name").textContent = event.name;
        clone.querySelector("#ev-res-date").textContent = event.date;
        clone.querySelector("#ev-res-id").addEventListener("click", function() { selectEvent(event.event_id); });
        searchResults.appendChild(clone);
    }
}

function selectEvent(event_id) {
    window.location.replace("/post/postpage.html?event=" + event_id);
}