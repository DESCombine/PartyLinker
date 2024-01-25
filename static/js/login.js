/*if(window.matchMedia("(max-width: 767px)").matches){
    document.getElementById("extern-container").classList.add("d-flex");
    document.getElementById("extern-container").classList.add("align-middle");
}*/

// when screen is resized
window.addEventListener("resize", function(){
    if(window.matchMedia("(min-width: 767px)").matches){
        document.getElementById("extern-container").classList.add("d-flex");
        document.getElementById("extern-container").classList.add("align-middle");
        this.document.getElementById("first").classList.add("align-items-center");
    } else {
        document.getElementById("extern-container").classList.remove("d-flex");
        document.getElementById("extern-container").classList.remove("align-middle");
        this.document.getElementById("first").classList.remove("align-items-center");
    }
});