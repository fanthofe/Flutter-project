
document.addEventListener("DOMContentLoaded", () => {
    const inputPassword = document.getElementById("inputPassword");
    document.getElementById("show_password").addEventListener("click", () => {
        inputPassword.getAttribute("type") === "password" ? inputPassword.setAttribute("type", "text") : inputPassword.setAttribute("type", "password");
    });
});
