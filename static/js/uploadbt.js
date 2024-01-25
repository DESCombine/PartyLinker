import { request_path } from "/static/js/config.js?v=2";
import { checkOrganizer, cleanTemplateList } from "/static/js/utils.js";

const modal = document.getElementById("upload-modal");

modal.addEventListener("shown.bs.modal", function() { renderModalFooter() });

async function renderModalFooter() {
    const modalFooter = modal.querySelector(".modal-footer");
    if (checkOrganizer() == 1) {
        modalFooter.querySelector("button").classList.remove("invisible");
        modalFooter.querySelector("button").addEventListener("click", function() { selectEvent(0); });
    } else {
        modalFooter.innerHTML = "<p class='text-center'>Had Fun? <i class='fa-solid fa-face-grin-stars'></i></p>";
    }
}

const modalBody = modal.querySelector(".modal-body");
const searchbar = modalBody.querySelector("input");
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
    const searchResults = modalBody.querySelector("ol");
    cleanTemplateList(searchResults);
    const events = await search(event);
    const template = modalBody.querySelector("template");
    for (let i = 0; i < events.length; i++) {
        const event = events[i];
        const clone = document.importNode(template.content, true);
        clone.querySelector("li").setAttribute("name", "event"+event.event_id);
        clone.querySelector("img").src = "/static/img/uploads/" + event.image;
        clone.querySelector("a").textContent = event.name;
        clone.querySelector("a").href = "/post/postpage.html?event=" + event.event_id;
        clone.querySelector("p").textContent = event.date;
        searchResults.appendChild(clone);
    }
}

function selectEvent(event_id) {
    window.location.replace("/post/postpage.html?event=" + event_id);
}