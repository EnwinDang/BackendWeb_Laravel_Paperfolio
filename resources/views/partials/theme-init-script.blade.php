<script>
    (function () {
        var theme = localStorage.getItem('cryptohub-theme') || 'light';
        document.documentElement.setAttribute('data-theme', theme);
        var sidebar = localStorage.getItem('cryptohub-sidebar') === 'collapsed' ? 'collapsed' : 'expanded';
        document.documentElement.setAttribute('data-sidebar', sidebar);
    })();
</script>
