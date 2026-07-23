document.addEventListener('DOMContentLoaded', () => {
  document.querySelectorAll('.product-order-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const panel = btn.parentElement.querySelector('.order-contact-panel');
      if (panel) {
        panel.style.display = panel.style.display === 'none' ? '' : 'none';
      }
    });
  });
});
