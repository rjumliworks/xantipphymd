/**
 * Contact form — VISUAL PROTOTYPE.
 *
 * Nothing is sent, stored or logged: submitting only validates, clears the
 * form and shows a message. Replace with a secure backend (encrypted storage,
 * consent, audit trail) before accepting real requests.
 */
export function initForm() {
  const form = document.querySelector('[data-prototype-form]');
  if (!form) return;

  const status = form.querySelector('[data-status]');

  form.addEventListener('submit', (e) => {
    e.preventDefault();
    if (!form.reportValidity()) return;
    form.reset();
    status.hidden = false;
    status.focus();
  });

  form.addEventListener('input', () => { status.hidden = true; });
}
