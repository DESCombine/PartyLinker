import { request_path } from "/static/js/config.js?v=1";

document.getElementsByTagName("form")[0].action = request_path + "/auth/signin.php";