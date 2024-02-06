import { request_path } from "/static/js/config.js?v=9";
import { cleanTemplateList } from "/static/js/utils.js?v=9";

// Listens for the searchbar to change and calls the search function
const searchbar = document.getElementById("searchbar");
searchbar.addEventListener("keyup", function(event) {
    if(searchbar.value.length > 0) {
        search(searchbar.value);
    } else {
        clearResults();
    }
    
});

/**
 * Searches for users with the given query
 * @param {String} query the partial username to search for 
 */
async function search(query) {
    const response = await fetch(request_path + "/user/search_user.php?query=" + query);
    const users = await response.json();
    showSearchResults(users);
    //if the value changed while searching, check if it's empty
    if(searchbar.value.length == 0) {
        clearResults();
    }
}

/**
 * Clears the search results
 */
function clearResults() {
    const search_results = document.getElementById("searchresults");
    cleanTemplateList(search_results);
}

/**
 * Shows the search results on the page
 * @param {JSON} users the users to show
 */
function showSearchResults(users) {
    const search_results = document.getElementById("searchresults");
    clearResults();
    users.forEach(user => {
        const clone = search_results.querySelector("template").cloneNode(true);
        if(user.profile_photo == null) {
            clone.content.querySelector("img").src = "/static/img/default-profile.png";
        } else {
            clone.content.querySelector("img").src = "/static/img/uploads/" + user.profile_photo;
        }
        clone.content.querySelector("span").textContent = user.username;
        const user_li = document.createElement("li");
        user_li.appendChild(clone.content);
        user_li.classList.add("result");
        user_li.classList.add("row");
        user_li.setAttribute("role", "button")
        user_li.addEventListener("click", function() { redirect(user.username); });
        search_results.appendChild(user_li);
    });
}

// Redirects to the user's profile
window.redirect = (user_id) => {
    console.log("redirecting to " + user_id)
    window.location.href = "/profile?user=" + user_id;
}
