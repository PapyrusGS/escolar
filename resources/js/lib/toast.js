export function showToast(message, variant = 'info', duration = 4000) {
  if (typeof window === 'undefined') return;
  window.dispatchEvent(
    new CustomEvent('toast', { detail: { message, variant, duration } })
  );
}

export const toast = {
  info: (msg, dur) => showToast(msg, 'info', dur),
  success: (msg, dur) => showToast(msg, 'success', dur),
  warning: (msg, dur) => showToast(msg, 'warning', dur),
  danger: (msg, dur) => showToast(msg, 'danger', dur),
  error: (msg, dur) => showToast(msg, 'danger', dur),
};
