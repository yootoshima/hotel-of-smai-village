
document.addEventListener("DOMContentLoaded", function () {
    const toggle = document.getElementById("sidebarToggle");
    const sidebar = document.getElementById("adminSidebar");
    const main = document.getElementById("adminMain");

    if (!toggle || !sidebar || !main) return;

    toggle.addEventListener("click", function () {
        const isCollapsed = sidebar.classList.toggle("collapsed");
        main.classList.toggle("sidebar-open", !isCollapsed);
        toggle.setAttribute("aria-expanded", String(!isCollapsed));
    });
});

