document.addEventListener('DOMContentLoaded', function () {
  const modal = document.getElementById('adminModal');
  const modalContent = document.getElementById('modalContent');
  const modalClose = document.getElementById('modalClose');

  function openModal(html) {
    modalContent.innerHTML = html;
    modal.setAttribute('aria-hidden', 'false');
  }

  function closeModal() {
    modal.setAttribute('aria-hidden', 'true');
    modalContent.innerHTML = '';
  }

  modalClose.addEventListener('click', closeModal);
  modal.addEventListener('click', function (e) {
    if (e.target === modal) closeModal();
  });

  // open modal when a button with class 'open-modal' is clicked
  document.body.addEventListener('click', function (e) {
    const btn = e.target.closest('.open-modal');
    if (btn) {
      const url = btn.dataset.url;
      if (!url) return;
      fetch(url, { headers: { 'X-Requested-With': 'XMLHttpRequest' } })
        .then(r => r.text())
        .then(html => openModal(html))
        .catch(() => openModal('<p>Erro ao carregar formulário.</p>'));
      return;
    }

    // tab switching
    const tab = e.target.closest('.tab');
    if (tab) {
      const which = tab.dataset.tab;
      document.querySelectorAll('.tab').forEach(t => t.classList.remove('active'));
      tab.classList.add('active');
      document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
      const panel = document.getElementById('tab-' + which);
      if (panel) panel.style.display = '';
    }
  });

  // Handle form submit inside modal via AJAX
  modal.addEventListener('submit', function (e) {
    e.preventDefault();
    const form = e.target;
    const action = form.getAttribute('action') || window.location.href;
    const method = (form.getAttribute('method') || 'POST').toUpperCase();
    const formData = new FormData(form);

    fetch(action, { method, body: formData, headers: { 'X-Requested-With': 'XMLHttpRequest' } })
      .then(r => r.text())
      .then(text => {
        // try to replace modal with server response
        openModal(text);
        // simple heuristic: if response contains 'success' redirect or refresh
        if (text.toLowerCase().includes('salvo') || text.toLowerCase().includes('sucesso')) {
          setTimeout(() => window.location.reload(), 900);
        }
      })
      .catch(() => openModal('<p>Erro ao enviar formulário.</p>'));
  });

  // Simple client-side search/filter for tables
  function tableFilter(inputId, tableSelector) {
    const input = document.getElementById(inputId);
    if (!input) return;
    input.addEventListener('input', function () {
      const q = input.value.toLowerCase();
      const rows = document.querySelectorAll(tableSelector + ' tbody tr');
      rows.forEach(r => {
        const text = r.innerText.toLowerCase();
        r.style.display = text.includes(q) ? '' : 'none';
      });
    });
  }

  tableFilter('searchUsers', '#tab-users table');
  tableFilter('searchMedicos', '#tab-medicos table');
  tableFilter('searchHorarios', '#tab-horarios table');
});
