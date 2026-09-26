/**
 * Consultation request — VISUAL PROTOTYPE.
 *
 * Nothing is sent, stored or logged: the submit handler only validates,
 * clears the form and shows a message. Replace with a secure backend
 * (encrypted storage, consent, audit trail) before accepting real requests.
 */
export function initConsult() {
  const dialog = document.getElementById('consult-dialog');
  if (!dialog) return;

  const form = dialog.querySelector('[data-prototype-form]');
  const status = dialog.querySelector('[data-status]');
  let opener = null;

  document.querySelectorAll('[data-open-consult]').forEach((btn) => {
    btn.addEventListener('click', () => {
      opener = btn;
      if (typeof dialog.showModal === 'function') dialog.showModal();
      else dialog.setAttribute('open', '');
    });
  });

  dialog.querySelectorAll('[data-close]').forEach((btn) => btn.addEventListener('click', () => dialog.close()));

  // Clicking the backdrop (the dialog element itself, outside its inner panel) closes it.
  dialog.addEventListener('click', (e) => { if (e.target === dialog) dialog.close(); });

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    if (!form.reportValidity()) return;
    form.reset();
    status.hidden = false;
    status.focus();
  });

  dialog.addEventListener('close', () => {
    form.reset();
    status.hidden = true;
    opener?.focus({ preventScroll: true });
  });
}
