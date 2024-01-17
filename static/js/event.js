import { request_path } from "/static/js/config.js";
import { loadEvent } from "/static/js/utils.js";
const posterA = document.getElementById("nav-poster");
const photosA = document.getElementById("nav-photos");
const peopleA = document.getElementById("nav-people");
const posterDiv = document.getElementById("poster");
const photosDiv = document.getElementById("photos");
const peopleDiv = document.getElementById("people");

posterA.addEventListener("click", function () { changeView("poster"); });
photosA.addEventListener("click", function () { changeView("photos"); });
peopleA.addEventListener("click", function () { changeView("people"); });

async function changeView(button) {
    let type = 0;
    //removeAll();
    if (button === "poster") {
        posterA.classList.add("active");
        peopleA.classList.remove("active");
        photosA.classList.remove("active");
        posterDiv.classList.remove("invisible");
        photosDiv.classList.add("invisible");
        peopleDiv.classList.add("invisible");
        type = 0;
    } else if (button === "photos") {
        photosA.classList.add("active");
        posterA.classList.remove("active");
        peopleA.classList.remove("active");
        posterDiv.classList.add("invisible");
        photosDiv.classList.remove("invisible");
        peopleDiv.classList.add("invisible");
        type = 1;
    } else if (button === "people") {
        peopleA.classList.add("active");
        posterA.classList.remove("active");
        photosA.classList.remove("active");
        posterDiv.classList.add("invisible");
        photosDiv.classList.add("invisible");
        peopleDiv.classList.remove("invisible");
        type = 2;
    }
    showContent(type);
}

async function showContent(type) {
    if(type === 0) {
        //showPosts();
        let poster = await loadEvent(event_id);
    } else if(type === 1) {
        showPhotos();
    } else if(type === 2) {
        showPeople();
    }
}