/* ASII-10 — Prototipo navegable (Semana 11)
   Comportamiento minimo para demostrar las reglas de interaccion de las
   Semanas 8-10: modal con foco atrapado (RI-08), toast anunciado por
   lectores de pantalla (RI-09) y acordeon accesible por teclado (RI-11). */

function initConfirmModal() {
  const openBtn = document.querySelector('[data-open-modal]');
  const overlay = document.querySelector('.modal-overlay');
  if (!openBtn || !overlay) return;

  const cancelBtn = overlay.querySelector('[data-cancel-modal]');
  const confirmBtn = overlay.querySelector('[data-confirm-modal]');
  const focusable = overlay.querySelectorAll('button, [href]');
  let lastFocused = null;

  function open() {
    lastFocused = document.activeElement;
    overlay.hidden = false;
    focusable[0].focus();
    document.addEventListener('keydown', onKeydown);
  }

  function close() {
    overlay.hidden = true;
    document.removeEventListener('keydown', onKeydown);
    if (lastFocused) lastFocused.focus();
  }

  function onKeydown(event) {
    if (event.key === 'Escape') {
      close();
      return;
    }
    if (event.key !== 'Tab') return;

    const first = focusable[0];
    const last = focusable[focusable.length - 1];
    if (event.shiftKey && document.activeElement === first) {
      event.preventDefault();
      last.focus();
    } else if (!event.shiftKey && document.activeElement === last) {
      event.preventDefault();
      first.focus();
    }
  }

  openBtn.addEventListener('click', open);
  cancelBtn.addEventListener('click', close);
  if (confirmBtn && confirmBtn.tagName === 'BUTTON') {
    confirmBtn.addEventListener('click', close);
  }
}

function initAccordions() {
  document.querySelectorAll('.accordion-trigger').forEach((trigger) => {
    trigger.addEventListener('click', () => {
      const panel = document.getElementById(trigger.getAttribute('aria-controls'));
      const expanded = trigger.getAttribute('aria-expanded') === 'true';
      trigger.setAttribute('aria-expanded', String(!expanded));
      panel.hidden = expanded;
    });
  });
}

function currentRole() {
  const fromQuery = new URLSearchParams(window.location.search).get('rol');
  if (fromQuery) {
    try { sessionStorage.setItem('asii10-rol', fromQuery); } catch (e) { /* prototipo: si no hay storage, se ignora */ }
    return fromQuery;
  }
  try { return sessionStorage.getItem('asii10-rol') || 'recepcionista'; }
  catch (e) { return 'recepcionista'; }
}

/* RI-01/RI-02 (Semana 8): el botón de acción visible depende del rol de la
   sesión. Aquí se simula con data-role en vez de un store de Pinia real. */
function initRoleAwareUI() {
  const role = currentRole();
  const label = role === 'medico' ? 'Médico / Enfermera' : 'Recepcionista';

  document.querySelectorAll('[data-role-badge]').forEach((el) => { el.textContent = label; });
  document.querySelectorAll('[data-role]').forEach((el) => {
    el.hidden = el.getAttribute('data-role') !== role;
  });
  document.querySelectorAll('a.role-link').forEach((link) => {
    const url = new URL(link.href);
    url.searchParams.set('rol', role);
    link.href = url.pathname.split('/').pop() + url.search;
  });
}

document.addEventListener('DOMContentLoaded', () => {
  initConfirmModal();
  initAccordions();
  initRoleAwareUI();
});
