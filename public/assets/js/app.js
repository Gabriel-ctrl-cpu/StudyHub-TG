document.addEventListener('DOMContentLoaded', () => {
    const toggleBtn = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('appSidebar');

    if (toggleBtn && sidebar) {
        toggleBtn.addEventListener('click', () => {
            sidebar.classList.toggle('open');
        });

        document.addEventListener('click', (event) => {
            if (
                sidebar.classList.contains('open') &&
                !sidebar.contains(event.target) &&
                !toggleBtn.contains(event.target)
            ) {
                sidebar.classList.remove('open');
            }
        });
    }
});
