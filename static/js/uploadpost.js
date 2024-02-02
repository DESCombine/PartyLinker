import { request_path } from "/static/js/config.js?v=210";
import { loadEvent } from "/static/js/utils.js?";

const event_id = new URLSearchParams(window.location.search).get('event');
const event = await loadEvent(event_id);

if (event_id == 0) {
    document.getElementsByTagName("h1")[0].innerHTML = "Upload new event";
    document.querySelector("#event-inputs").classList.remove("invisible");
    const logo = document.querySelector("form img");
    logo.parentNode.remove();
} else {
    document.getElementsByTagName("h1")[0].innerHTML = "Upload photo for " + event.name;
    document.querySelector("form div").classList.add("overflow-y-hidden");
    document.querySelectorAll("#event-inputs input").forEach(input => {
        input.removeAttribute("required");
    });
}

document.getElementsByName("event-id")[0].value = event_id;
document.getElementsByTagName("form")[0].action = request_path + "/user/upload_post.php";

document.querySelector("#inputLocation").onkeyup = function (e) {
    var code = (e.keyCode || e.which)
    console.log(code)
    if(this.value.length > 4 && (code != 37 || code != 38 || code != 39 || code != 40)) {
        //document.querySelector(".location-suggestions").classList.remove("invisible");
        get_location_suggestions(this.value);
    }
}

async function get_location_suggestions(location) {
    mapboxgl.accessToken = 'pk.eyJ1IjoiZGFuaWxvbWFnbGlhIiwiYSI6ImNscmdtYzVqYTAyejIya21rZnJrOWtsazIifQ.4iM5ZZ26Y945WvEawTztOQ';
    //check if a session_id is in the url
    const url = new URL(window.location.href);
    let session_id = url.searchParams.get('session_id');

    if (session_id == null) {
        url.searchParams.set('session_id', crypto.randomUUID());
        window.history.replaceState({}, '', url);
        session_id = url.searchParams.get('session_id');
    }

    const uuid_pattern = /^[0-9a-fA-F]{8}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{4}\b-[0-9a-fA-F]{12}$/gi;
    if(uuid_pattern.test(session_id)) {
        
        const request_url = `https://api.mapbox.com/search/searchbox/v1/suggest?q=${location.replaceAll(" ", "%20")}&types=address,poi,street,city&access_token=${mapboxgl.accessToken}&session_token=${session_id}`;
        const data = await (await fetch(request_url)).json();
        let suggestions = []
        data.suggestions.forEach(suggestion => {
            console.log(suggestion)
            suggestions.push({
                "name": suggestion.name,
                "address": suggestion.full_address,
                "mapbox_id": suggestion.mapbox_id,
            })
        });
        
        let suggestions_html = "";
        suggestions.forEach(suggestion => {
            suggestions_html += `<div class="suggestion" onclick="suggestion_selected('${suggestion.mapbox_id}')">
                                    <p class="location-name fs-5 fw-bold">${suggestion.name}</p>
                                    ${suggestion.address!=undefined?'<p class="location-address">' + suggestion.address + '</p>':''}
                                </div>`;
        });
        document.querySelector("#locationSuggestions").innerHTML = suggestions_html;
        document.querySelector("#locationSuggestions").classList.remove("d-none");
    }
}

window.suggestion_selected = async function (mapbox_id) {
    const session_id = new URLSearchParams(window.location.search).get('session_id');
    const request_url = `https://api.mapbox.com/search/searchbox/v1/retrieve/${mapbox_id}?access_token=${mapboxgl.accessToken}&session_token=${session_id}`;
    const data = await (await fetch(request_url)).json();
    console.log(data)
    document.querySelector("#inputLocation").value = data.features[0].properties.full_address;
    document.querySelector("#locationSuggestions").classList.add("d-none");
    //Remove session_id from url
    const url = new URL(window.location.href);
    url.searchParams.delete('session_id');
    window.history.replaceState({}, '', url);
}