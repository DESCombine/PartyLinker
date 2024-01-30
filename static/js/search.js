import { request_path } from "/static/js/config.js?v=2";

const searchbar = document.getElementById("searchbar");
searchbar.addEventListener("keyup", function(event) {
    if(searchbar.value.length > 0) {
        search(searchbar.value);
    } else {
        clearResults();
    }
    
});

async function search(query) {
    const response = await fetch(request_path + "/user/search_user.php?query=" + query);
    const users = await response.json();
    showSearchResults(users);
    //if the value changed while searching, check if it's empty
    if(searchbar.value.length == 0) {
        clearResults();
    }
}

function clearResults() {
    const search_results = document.getElementById("searchresults");
    search_results.innerHTML = "";
}

function showSearchResults(users) {
    const search_results = document.getElementById("searchresults");
    search_results.innerHTML = "";
    users.forEach(user => {
        const user_div = document.createElement("div");
        user_div.innerHTML = `
        <div class="result row" onclick="redirect('${user.username}')">
            <div class="col-2">
                <img src="/static/img/uploads/${user.profile_photo}" alt="placeholder" class="result-profile-img"> 
            </div> 
            <div class="col-9"> 
                <span>${user.username}</span>  
            </div> 
        </div>`
        search_results.appendChild(user_div);
    });
}


window.redirect = (user_id) => {
    window.location.href = "/profile?user=" + user_id;
}
