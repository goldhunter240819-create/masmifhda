<!-- Mobile overlay for sidebar -->
<div id="zOverlay" onclick="zToggleSidebar()" style="display:none;position:fixed;inset:0;background:rgba(0,0,0,.4);z-index:299;backdrop-filter:blur(2px);"></div>

<script>
lucide.createIcons();

function zToggleSidebar() {
    const sidebar = document.getElementById('zSidebar');
    const overlay = document.getElementById('zOverlay');
    if(sidebar) {
        sidebar.classList.toggle('open');
        overlay.style.display = sidebar.classList.contains('open') ? 'block' : 'none';
    }
}
</script>
</body>
</html>
