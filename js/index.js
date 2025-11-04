document.addEventListener("DOMContentLoaded", function() {
    // Solo se usa en el dashboard
    const menu = document.getElementById('mainMenu');
    const hamburger = document.getElementById('hamburgerIcon');
    if (menu && hamburger) {
        hamburger.addEventListener('click', function() {
            menu.classList.toggle('menu-hidden');
        });
    }

    // Solo se usa en el login
    const togglePassword = document.querySelector("#togglePassword");
    const password = document.querySelector("#pass");

    if (togglePassword && password) {
        togglePassword.addEventListener("click", function () {
            const type = password.getAttribute("type") === "password" ? "text" : "password";
            password.setAttribute("type", type);
    
            this.classList.toggle('fa-eye');
            this.classList.toggle('fa-eye-slash');
        });
    }
});