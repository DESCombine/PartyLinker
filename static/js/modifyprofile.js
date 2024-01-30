import { request_path } from "/static/js/config.js?v=2";

document.getElementsByTagName("form")[0].action = request_path + "/user/modifyprofile.php";

if (window.matchMedia("(min-width: 767px)").matches) {
    document.getElementsByTagName('img')[0].classList.add('d-none')
}

window.addEventListener("resize", function(){
    if(window.matchMedia("(min-width: 767px)").matches){
        document.getElementsByTagName('img')[0].classList.add('d-none')
        this.document.getElementById('extern-container').classList.add('d-flex')
    } else {
        document.getElementsByTagName('img')[0].classList.remove('d-none')
        this.document.getElementById('extern-container').classList.remove('d-flex')
    }
});

async function loadPosts(user) {
    const request_url = user == null ? request_path + "/user/load_posted.php" : request_path + "/user/load_posted.php?user=" + user;
    const response = await fetch(request_url, {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    });
    const posts = await response.json();
    return posts;
}

async function showSavedInformations() {
    // Get informations from server
    const request_url = request_path + "/user/get_saved_infos.php";
    const response = await fetch(request_url, {
        method: "GET",
        headers: {
            "Content-Type": "application/json"
        },
        credentials: "include"
    });
    const data = await response.json();
    console.log(data);

    // for each empty element in data, set it to ""
    for (let i = 0; i < data.length; i++) {
        for (const [key, value] of Object.entries(data[i])) {
            if (value == null) {
                data[i][key] = "";
            }
        }
    }

    // Show informations in form
    document.getElementById("inputName").value = data[0].name;
    document.getElementById("inputSurname").value = data[0].surname;
    document.getElementById("inputDate").value = data[0].birth_date;
    document.getElementById("inputEmail").value = data[0].email;
    document.getElementById("inputPhone").value = data[0].phone;
    // document.getElementById("inputGender").value = data[0].gender;
    document.getElementById("inputOrganizer").checked = data[1].organizer == 1 ? true : false;
    document.getElementById("inputBio").value = data[0].bio;
    document.getElementById("inputLanguage").value = data[1].language;
    document.getElementById("inputNotifications").checked = data[1].notifications == 1 ? true : false;
    document.getElementById("input2FA").checked = data[1].twofa == 1 ? true : false;
}

showSavedInformations();