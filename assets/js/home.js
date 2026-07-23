document.addEventListener('DOMContentLoaded', function () {
  var refreshBtn = document.querySelector('.astro-error button');
  if (refreshBtn) {
    refreshBtn.addEventListener('click', function () { window.location.reload(); });
  }
});