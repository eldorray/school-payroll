export function setupDeleteConfirmation(dialog) {
    const document = dialog.ownerDocument;
    const approvedForms = new WeakSet();

    function confirmDeletion(message) {
        if (dialog.open) return Promise.resolve(false);
        dialog.querySelector('[data-confirm-message]').textContent = message;
        dialog.returnValue = 'cancel';
        return new Promise((resolve) => {
            dialog.addEventListener('close', () => resolve(dialog.returnValue === 'confirm'), { once: true });
            dialog.showModal();
        });
    }

    document.addEventListener('submit', async (event) => {
        const form = event.target;
        if (event.defaultPrevented || approvedForms.delete(form) || form.hasAttribute('data-delete-confirmed')) return;
        const method = form.querySelector('[name="_method"]')?.value.toUpperCase();
        const removed = form.querySelectorAll('[name="remove[]"]:checked').length;
        if (method !== 'DELETE' && !removed) return;

        event.preventDefault();
        const message = removed
            ? `Hapus ${removed} guru dari batch ini? Data gaji mereka akan dihapus permanen.`
            : form.dataset.deleteConfirmation || 'Hapus data ini? Tindakan ini tidak dapat dibatalkan.';
        const submitter = event.submitter;
        if (await confirmDeletion(message)) {
            approvedForms.add(form);
            try {
                form.requestSubmit(submitter || undefined);
            } finally {
                approvedForms.delete(form);
            }
        }
    });

    return confirmDeletion;
}
