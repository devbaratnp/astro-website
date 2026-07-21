    </main>
</div>

<script>
(function(){
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var toggle  = document.getElementById('sidebarToggle');
    var closeBtn = document.getElementById('sidebarClose');
    var lastFocused = null;

    function isDesktop() { return window.matchMedia('(min-width: 1024px)').matches; }

    function openSidebar() {
        if (isDesktop()) return;
        lastFocused = document.activeElement;
        sidebar.classList.add('open');
        overlay.classList.add('active');
        toggle.setAttribute('aria-expanded', 'true');
        document.body.style.overflow = 'hidden';
        if (closeBtn) setTimeout(function() { closeBtn.focus(); }, 100);
    }

    function closeSidebar() {
        if (isDesktop()) return;
        sidebar.classList.remove('open');
        overlay.classList.remove('active');
        toggle.setAttribute('aria-expanded', 'false');
        document.body.style.overflow = '';
        if (lastFocused) lastFocused.focus();
    }

    if (toggle) { toggle.addEventListener('click', function(e) { e.stopPropagation(); openSidebar(); }); }
    if (overlay) { overlay.addEventListener('click', closeSidebar); }
    if (closeBtn) { closeBtn.addEventListener('click', closeSidebar); }

    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape' && sidebar && sidebar.classList.contains('open')) closeSidebar();
        if (e.key !== 'Tab' || !sidebar || !sidebar.classList.contains('open') || isDesktop()) return;
        var focusable = sidebar.querySelectorAll('a[href], button:not([disabled])');
        if (!focusable.length) return;
        if (e.shiftKey && document.activeElement === focusable[0]) { e.preventDefault(); focusable[focusable.length - 1].focus(); }
        else if (!e.shiftKey && document.activeElement === focusable[focusable.length - 1]) { e.preventDefault(); focusable[0].focus(); }
    });

    var links = sidebar ? sidebar.querySelectorAll('.sidebar-link') : [];
    for (var i = 0; i < links.length; i++) {
        links[i].addEventListener('click', function() { if (!isDesktop()) setTimeout(closeSidebar, 150); });
    }

    window.addEventListener('resize', function() {
        if (isDesktop() && sidebar) {
            sidebar.classList.remove('open');
            if (overlay) overlay.classList.remove('active');
            toggle.setAttribute('aria-expanded', 'false');
            document.body.style.overflow = '';
        }
    });
})();
</script>
</body>
</html>
