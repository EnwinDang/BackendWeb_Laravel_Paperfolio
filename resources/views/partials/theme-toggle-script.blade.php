<script>
    function updateThemeIcon() {
        var icon = document.getElementById('theme-toggle-icon');
        if (icon) {
            icon.innerHTML = document.documentElement.getAttribute('data-theme') === 'dark' ? '&#9728;' : '&#9789;';
        }
    }
    function toggleTheme() {
        var next = document.documentElement.getAttribute('data-theme') === 'dark' ? 'light' : 'dark';
        document.documentElement.setAttribute('data-theme', next);
        localStorage.setItem('cryptohub-theme', next);
        updateThemeIcon();
    }
    function toggleSidebar() {
        var next = document.documentElement.getAttribute('data-sidebar') === 'collapsed' ? 'expanded' : 'collapsed';
        document.documentElement.setAttribute('data-sidebar', next);
        localStorage.setItem('cryptohub-sidebar', next);
    }
    updateThemeIcon();
</script>
