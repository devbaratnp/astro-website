(function() {
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var toggle  = document.getElementById('sidebarToggle');
    var closeBtn = document.getElementById('sidebarClose');
    var collapseBtn = document.getElementById('sidebarCollapseBtn');
    var adminWrapper = document.querySelector('.admin-wrapper');
    var lastFocused = null;

    function isDesktop() { return window.matchMedia('(min-width: 1024px)').matches; }

    function setCollapsedState(collapsed) {
        if (!sidebar) return;
        if (collapsed) {
            sidebar.classList.add('collapsed');
            if (adminWrapper) adminWrapper.classList.add('sidebar-collapsed');
        } else {
            sidebar.classList.remove('collapsed');
            if (adminWrapper) adminWrapper.classList.remove('sidebar-collapsed');
        }
        if (collapseBtn) {
            collapseBtn.setAttribute('aria-label', collapsed ? 'Expand sidebar' : 'Collapse sidebar');
            collapseBtn.title = collapsed ? 'Expand sidebar' : 'Collapse sidebar';
        }
    }

    function saveCollapsePref(collapsed) {
        try { localStorage.setItem('admin_sidebar_collapsed', collapsed ? '1' : '0'); } catch(e) {}
    }

    function restoreCollapsePref() {
        if (!isDesktop() || !sidebar) return;
        try {
            setCollapsedState(localStorage.getItem('admin_sidebar_collapsed') === '1');
        } catch(e) {}
    }

    function openSidebar() {
        if (isDesktop()) return;
        if (!sidebar || !toggle) return;
        lastFocused = document.activeElement;
        sidebar.classList.add('open');
        if (overlay) overlay.classList.add('active');
        toggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
        if (closeBtn) setTimeout(function() { closeBtn.focus(); }, 100);
    }

    function closeSidebar() {
        if (!sidebar || !toggle) return;
        sidebar.classList.remove('open');
        if (overlay) overlay.classList.remove('active');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        if (lastFocused && lastFocused.focus) lastFocused.focus();
    }

    function toggleSidebar() {
        if (!sidebar || !toggle) return;
        if (isDesktop()) {
            var collapsed = !sidebar.classList.contains('collapsed');
            setCollapsedState(collapsed);
            saveCollapsePref(collapsed);
        } else {
            if (sidebar.classList.contains('open')) {
                closeSidebar();
            } else {
                openSidebar();
            }
        }
    }

    function toggleCollapse() {
        if (!isDesktop() || !sidebar) return;
        var collapsed = !sidebar.classList.contains('collapsed');
        setCollapsedState(collapsed);
        saveCollapsePref(collapsed);
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
            if (!isDesktop()) closeSidebar();
        });
    }

    var wasDesktop = isDesktop();
    window.addEventListener('resize', function() {
        var nowDesktop = isDesktop();
        if (wasDesktop && !nowDesktop) {
            if (sidebar) sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('active');
            if (toggle) toggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        } else if (!wasDesktop && nowDesktop) {
            document.body.style.overflow = '';
            restoreCollapsePref();
        }
        wasDesktop = nowDesktop;
    });

    window.addEventListener('beforeunload', function() {
        document.body.style.overflow = '';
    });

    restoreCollapsePref();
})();
