import axios from "axios";
window.axios = axios;

window.axios.defaults.headers.common["X-Requested-With"] = "XMLHttpRequest";
// ✅ CSRF token (Laravel)
const tokenMeta = document.querySelector('meta[name="csrf-token"]');
if (tokenMeta) {
    window.axios.defaults.headers.common["X-CSRF-TOKEN"] =
        tokenMeta.getAttribute("content");
}

// ✅ Alpine.js
import Alpine from 'alpinejs';
window.Alpine = Alpine;

// Defer Alpine.start() to allow inline scripts in Blade templates
// to register their alpine:init event listeners first
document.addEventListener('DOMContentLoaded', () => {
    Alpine.start();
});
