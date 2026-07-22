document.addEventListener('DOMContentLoaded', () => {
  const menuBtn = document.getElementById('mobile-menu-btn');
  const nav = document.getElementById('main-nav');
  if (menuBtn && nav) {
    menuBtn.addEventListener('click', () => {
      const open = nav.classList.toggle('open');
      menuBtn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });
  }

  // Ticking clock for home page element if present
  const clockEl = document.querySelector('.astro-clock');
  if (clockEl) {
    const fmt = new Intl.DateTimeFormat('ne-NP', {
      hour: '2-digit', minute: '2-digit', second: '2-digit',
      timeZone: 'Asia/Kathmandu', hour12: false
    });
    const tick = () => {
      clockEl.textContent = fmt.format(new Date());
    };
    tick();
    setInterval(tick, 1000);
  }
});
