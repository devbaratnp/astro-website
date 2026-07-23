document.addEventListener('DOMContentLoaded', () => {
  const tabs = document.querySelectorAll('.gallery-tabs .tab-btn');
  const grid = document.getElementById('gallery-grid');
  const lightbox = document.getElementById('lightbox');
  const lightboxImg = document.getElementById('lightbox-img');
  const lightboxClose = document.getElementById('lightbox-close');
  let currentType = 'all';

  tabs.forEach(btn => {
    btn.addEventListener('click', () => {
      tabs.forEach(b => b.classList.remove('active'));
      btn.classList.add('active');
      currentType = btn.dataset.type;
      Array.from(grid.querySelectorAll('.gallery-item')).forEach(item => {
        item.style.display = currentType === 'all' || item.dataset.type === currentType ? '' : 'none';
      });
    });
  });

  grid.addEventListener('click', e => {
    const photo = e.target.closest('.gallery-photo');
    if (!photo) return;
    lightboxImg.src = photo.dataset.url;
    lightboxImg.alt = photo.dataset.title;
    lightbox.style.display = '';
  });

  const closeLightbox = () => {
    lightbox.style.display = 'none';
    lightboxImg.src = '';
  };

  lightboxClose.addEventListener('click', e => { e.stopPropagation(); closeLightbox(); });
  lightbox.addEventListener('click', closeLightbox);
  document.addEventListener('keydown', e => { if (e.key === 'Escape') closeLightbox(); });
});
