document.addEventListener('DOMContentLoaded', () => {
  const tabs = document.querySelectorAll('.events-tabs .tab-btn');
  const contents = {
    upcoming: document.getElementById('events-upcoming'),
    tour: document.getElementById('events-tour'),
  };
  tabs.forEach(btn => {
    btn.addEventListener('click', () => {
      tabs.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      Object.values(contents).forEach(c => { if (c) c.style.display = 'none'; });
      const target = contents[btn.dataset.tab];
      if (target) target.style.display = '';
    });
  });
});
