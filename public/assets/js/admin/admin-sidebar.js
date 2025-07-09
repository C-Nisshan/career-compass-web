document.addEventListener("DOMContentLoaded", () => {
    const toggleSidebar = document.getElementById("admin-toggle-sidebar");
    const sidebar = document.getElementById("admin-sidebar");
    const sectionToggles = document.querySelectorAll(".admin-sidebar-section-toggle");

    // Mobile sidebar toggle
    if (toggleSidebar && sidebar) {
        toggleSidebar.addEventListener("click", () => {
            sidebar.classList.toggle("show");
        });
    }

    // Collapsible section toggle
    sectionToggles.forEach(toggle => {
        toggle.addEventListener("click", (e) => {
            e.preventDefault(); // Prevent default anchor behavior
            const sectionId = toggle.getAttribute("data-section");
            const submenu = document.getElementById(`admin-sidebar-${sectionId}`);
            
            // Toggle current submenu
            const isShown = submenu.classList.contains("show");
            submenu.classList.toggle("show", !isShown);

            // Collapse other submenus
            document.querySelectorAll(".admin-sidebar-submenu").forEach(otherSubmenu => {
                if (otherSubmenu !== submenu) {
                    otherSubmenu.classList.remove("show");
                }
            });
        });
    });
});