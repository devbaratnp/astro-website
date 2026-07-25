</main>
</div>

<script>
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var toggle  = document.getElementById('sidebarToggle');
    var closeBtn = document.getElementById('sidebarClose');
    var collapseBtn = document.getElementById('sidebarCollapseBtn');
    var adminWrapper = document.querySelector('.admin-wrapper');
    var lastFocused = null;

    function isDesktop() { return window.matchMedia('(min-width: 1024px)').matches; }

    function openSidebar() {
        if (isDesktop()) return;
        if (!sidebar || !overlay || !toggle) return;
        lastFocused = document.activeElement;
        sidebar.classList.add('open');
        overlay.classList.add('active');
        toggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
        if (closeBtn) setTimeout(function() { closeBtn.focus(); }, 100);
    }

    function closeSidebar() {
        if (!sidebar || !overlay || !toggle) return;
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        if (lastFocused && lastFocused.focus) lastFocused.focus();
    }

    function toggleSidebar() {
        if (!sidebar || !toggle) return;
        if (isDesktop()) {
            sidebar.classList.toggle('collapsed');
            if (adminWrapper) adminWrapper.classList.toggle('sidebar-collapsed');
            var isCollapsed = sidebar.classList.contains('collapsed');
            if (collapseBtn) {
                collapseBtn.setAttribute('aria-label', isCollapsed ? 'Expand sidebar' : 'Collapse sidebar');
                collapseBtn.title = isCollapsed ? 'Expand sidebar' : 'Collapse sidebar';
            }
            try { localStorage.setItem('admin_sidebar_collapsed', isCollapsed ? '1' : '0'); } catch(e) {}
        } else {
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }
    }

    function toggleCollapse() {
        if (!isDesktop()) return;
        sidebar.classList.toggle('collapsed');
        if (adminWrapper) adminWrapper.classList.toggle('sidebar-collapsed');
        var isCollapsed = sidebar.classList.contains('collapsed');
        if (collapseBtn) {
            collapseBtn.setAttribute('aria-label', isCollapsed ? 'Expand sidebar' : 'Collapse sidebar');
            collapseBtn.title = isCollapsed ? 'Expand sidebar' : 'Collapse sidebar';
        }
        try { localStorage.setItem('admin_sidebar_collapsed', isCollapsed ? '1' : '0'); } catch(e) {}
    }

    function restoreCollapsePref() {
        if (!isDesktop() || !sidebar) return;
        try {
            if (localStorage.getItem('admin_sidebar_collapsed') === '1') {
                sidebar.classList.add('collapsed');
                if (adminWrapper) adminWrapper.classList.add('sidebar-collapsed');
                if (collapseBtn) {
                    collapseBtn.setAttribute('aria-label', 'Expand sidebar');
                    collapseBtn.title = 'Expand sidebar';
                }
            }
        } catch(e) {}
    }

    if (toggle) { toggle.addEventListener('click', function(e) { e.stopPropagation(); toggleSidebar(); }); }
    if (overlay) { overlay.addEventListener('click', closeSidebar); }
    if (closeBtn) { closeBtn.addEventListener('click', closeSidebar); }
    if (collapseBtn) { collapseBtn.addEventListener('click', function(e) { e.stopPropagation(); toggleCollapse(); }); }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open')) {
            closeSidebar();
        }
    });

    var links = sidebar ? sidebar.querySelectorAll('.sidebar-link') : [];
    for (var i = 0; i < links.length; i++) {
        links[i].addEventListener('click', function() {
            if (!isDesktop()) setTimeout(closeSidebar, 150);
        });
    }

    window.addEventListener('resize', function() {
        if (isDesktop()) {
            if (sidebar) sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('active');
            if (toggle) toggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        } else {
            if (sidebar) sidebar.classList.remove('collapsed');
            if (adminWrapper) adminWrapper.classList.remove('sidebar-collapsed');
        }
    });

    restoreCollapsePref();
</script>
<script>
    var toggleBtns = document.querySelectorAll('[onclick*="toggleDetails"], [onclick*="toggleMsg"]');
    for (var i = 0; i < toggleBtns.length; i++) {
        var btn = toggleBtns[i];
        var onclick = btn.getAttribute('onclick');
        if (onclick && onclick.indexOf('toggleDetails') !== -1) {
            btn.setAttribute('aria-expanded', 'false');
            btn.addEventListener('click', function() {
                var expanded = this.getAttribute('aria-expanded') === 'true';
                this.setAttribute('aria-expanded', expanded ? 'false' : 'true');
            });
        }
    }
</script>
</body>
</html>