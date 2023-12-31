const searchbar = document.getElementById("searchbar");
searchbar.addEventListener("keyup", function(event) {
    if (event.key === 'Enter') {
        search(searchbar.value);
    }
});

async function search(query) {
    const response = await fetch("https://api.partylinker.live/user/search_user.php?query=" + query);
    const users = await response.json();
    showSearchResults(users);
}

function showSearchResults(users) {
    const search_results = document.getElementById("searchresults");
    search_results.innerHTML = "";
    users.forEach(user => {
        const user_div = document.createElement("div");
        user_div.innerHTML = `
        <div class="result row">
            <div class="col-2">
                <img src="/static/img/uploads/${user.photo}" alt="placeholder" class="img-fluid result-profile-img"> 
            </div> 
            <div class="col-9"> 
                <span>${user.username}</span>  
            </div> 
        </div>`
        search_results.appendChild(user_div);
    });
}

