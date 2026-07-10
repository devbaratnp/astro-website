    </main>
</div>

<script>
(function(){
    var sidebar = document.getElementById('sidebar');
    var overlay = document.getElementById('sidebarOverlay');
    var toggle  = document.getElementById('sidebarToggle');

    if(toggle){
        toggle.addEventListener('click', function(){
            sidebar.classList.toggle('open');
            overlay.classList.toggle('active');
        });
    }
    if(overlay){
        overlay.addEventListener('click', function(){
            sidebar.classList.remove('open');
            overlay.classList.remove('active');
        });
    }
})();
</script>
</body>
</html>
