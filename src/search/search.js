async function search() {
    const search_query = document.getElementById("searchbar").value;
    const response = await fetch("https://api.partylinker.live/user/search_user.php?query=" + search_query);
    const users = await response.json();
    showSearchResults(users);
}

function showSearchResults(users) {
    const search_results = document.getElementById("searchresults");
    search_results.innerHTML = "";
    users.forEach(user => {
        const user_div = document.createElement("div");
        user_div.innerHTML = user.username;
        search_results.appendChild(user_div);
    });
}