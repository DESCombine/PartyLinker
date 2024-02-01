import { request_path } from "/static/js/config.js?v=2";

document.getElementsByTagName("form")[0].action = request_path + "/user/modifyprofile.php";

if (window.matchMedia("(min-width: 767px)").matches) {
    document.getElementsByTagName('img')[0].classList.add('d-none')
}

window.addEventListener("resize", function () {
    if (window.matchMedia("(min-width: 767px)").matches) {
        document.getElementsByTagName('img')[0].classList.add('d-none')
        this.document.getElementById('extern-container').classList.add('d-flex')
    } else {
        document.getElementsByTagName('img')[0].classList.remove('d-none')
        this.document.getElementById('extern-container').classList.remove('d-flex')
    }
});

async function loadSavesInfos() {
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
    return data;
}

async function showSavedInformations() {

    let data = await loadSavesInfos();

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
    let lang = data[1].language;
    if(lang == "it") {
        document.getElementById("inputLanguage").value = "Italiano";
    } else if(lang == "en") {
        document.getElementById("inputLanguage").value = "English";
    } else if(lang == "fr") {
        document.getElementById("inputLanguage").value = "Français";
    } else if(lang == "de") {
        document.getElementById("inputLanguage").value = "Deutsch";
    } else if(lang == "es") {
        document.getElementById("inputLanguage").value = "Español";
    }
    document.getElementById("inputNotifications").checked = data[1].notifications == 1 ? true : false;
    document.getElementById("input2FA").checked = data[1].twofa == 1 ? true : false;
}

showSavedInformations();