    </main>
</div>

<script>
(function(){
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var toggle  = document.getElementById('sidebarToggle');

    function closeSidebar() {
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('active');
    }

    function openSidebar() {
        sidebar.classList.add('open');
        if (overlay) overlay.classList.add('active');
    }

    if (toggle) {
        toggle.addEventListener('click', function(e) {
            e.stopPropagation();
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        });
    }

    if (overlay) {
        overlay.addEventListener('click', closeSidebar);
    }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') closeSidebar();
    });

    var links = sidebar ? sidebar.querySelectorAll('.sidebar-link') : [];
    for (var i = 0; i < links.length; i++) {
        links[i].addEventListener('click', function() {
            if (window.innerWidth <= 900) closeSidebar();
        });
    }

    window.addEventListener('resize', function() {
        if (window.innerWidth > 900) closeSidebar();
    });
})();
</script>
</body>
</html>
