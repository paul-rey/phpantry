// Responsive menu
document.querySelector("#toggle_menu").addEventListener("click", function () {
    document.querySelector(".menu_items").classList.toggle("visible");
    this.classList.toggle("open");
});

