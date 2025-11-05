document.addEventListener("DOMContentLoaded", function() {
    // --- LÓGICA DEL MENÚ HAMBURGUESA ---
    const menu = document.getElementById('mainMenu'); // El contenedor <section class="menu" id="mainMenu">
    const hamburger = document.getElementById('hamburgerIcon');

    if (menu && hamburger) {
        hamburger.addEventListener('click', function() {
            // Aplicamos 'menu-hidden' al contenedor principal del menú
            menu.classList.toggle('menu-hidden'); 
        });
    }

    // --- LÓGICA DE LOGIN (Control de Visibilidad de Contraseña) ---
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