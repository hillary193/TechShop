// js/cookies.js
// Gestión de cookies de consentimiento (no conectada a ningún servicio externo, solo para recordar la elección del usuario)

function getCookie(name) {
    const nameEQ = name + "=";
    const cookies = document.cookie.split(';');
    for (let i = 0; i < cookies.length; i++) {
        let c = cookies[i].trim();
        if (c.indexOf(nameEQ) === 0) {
            const value = c.substring(nameEQ.length);
            return value;
        }
    }
    return null;
}
// Establece una cookie con el nombre, valor y duración en días
function setCookie(name, value, days = 365) {
    const date = new Date();
    date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
    const expires = "expires=" + date.toUTCString();
    document.cookie = name + "=" + value + ";" + expires + ";path=/;SameSite=Lax";
}

function initCookieBanner() {
    const cookieConsent = getCookie("cookieConsent");
    const banner = document.getElementById("cookieBanner");
    
    if (!banner) return;
    
    // Si ya hay consentimiento guardado, ocultar el banner
    if (cookieConsent) {
        banner.style.display = "none";
    } else {
        // Si NO hay consentimiento, mostrar el banner
        banner.style.display = "block";
    }
}

function acceptCookies() {
    setCookie("cookieConsent", "accepted", 365);
    const banner = document.getElementById("cookieBanner");
    if (banner) {
        banner.style.display = "none";
    }
}

function rejectCookies() {
    setCookie("cookieConsent", "rejected", 365);
    const banner = document.getElementById("cookieBanner");
    if (banner) {
        banner.style.display = "none";
    }
}

// Inicializar al cargar la página
document.addEventListener("DOMContentLoaded", initCookieBanner);
