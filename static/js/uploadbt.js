import { request_path } from "/static/js/config.js?v=13";
import { checkOrganizer, cleanTemplateList } from "/static/js/utils.js?v=13";

const modal = document.getElementById("upload-modal");
modal.addEventListener("shown.bs.modal", function() { renderModalFooter() });

/**
 * Renders the modal footer based on the user's role
 */
async function renderModalFooter() {
    const modalFooter = modal.querySelector(".modal-footer");
    if (await checkOrganizer() === 1) {
        modalFooter.querySelector("button").classList.remove("invisible");
        modalFooter.querySelector("button").addEventListener("click", 
            function() { window.location.replace("/post/postpage.html?event=0"); });
    } else {
        modalFooter.innerHTML = "<p class='text-center'>Had Fun? <em class='fa-solid fa-face-grin-stars'></em></p>";
    }
}

// Listens for the searchbar to change and calls the search function
const modalBody = modal.querySelector(".modal-body");
const searchbar = modalBody.querySelector("input");
searchbar.addEventListener("keyup", function(event) {
    if (searchbar.value.length > 0) {
        showSearchResults(searchbar.value);
    } else {
        clearResults();
    }
});

/**
 * Clears the search results
 */
function clearResults() {
    const searchResults = modalBody.querySelector("ol");
    cleanTemplateList(searchResults);
}

/**
 * Searches for events with the given query
 * @param {String} event the partial event name to search for
 * @returns  the events found
 */
async function search(event) {
    const response = await fetch(request_path + "/user/search_event.php?event=" + event);
    const events = await response.json();
    return events;
}

/**
 * Shows the search results on the page
 * @param {JSON} event the events to show 
 */
async function showSearchResults(event) {
    const searchResults = modalBody.querySelector("ol");
    const events = await search(event);
    const template = modalBody.querySelector("template");
    clearResults();
    for (let i = 0; i < events.length; i++) {
        let event = events[i];
        let clone = template.content.cloneNode(true);
        clone.querySelector("li").setAttribute("name", "event"+event.event_id);
        clone.querySelector("img").src = "/static/img/uploads/" + event.image;
        clone.querySelector("img").alt = event.name;
        clone.querySelector("a").textContent = event.name;
        clone.querySelector("a").href = "/post/postpage.html?event=" + event.event_id;
        clone.querySelector("p").textContent = event.date;
        searchResults.appendChild(clone);
    }
    //get the searchbar value
    const searchbar = modalBody.querySelector("input");
    if(searchbar.value.length == 0) {
        clearResults();
    }
}