/**
 * Global toast notification helper.
 * Usage:
 *   window.toast('success', 'Cliente eliminado');
 *   window.toast.success('Cliente eliminado');
 *   window.toast.error('Algo salió mal');
 *   window.toast.warning('Cuidado...');
 *   window.toast.info('Novedad');
 */
window.toast = function (type, message) {
    if (type === 'success' || type === 'error' || type === 'warning' || type === 'info') {
        window.dispatchEvent(new CustomEvent('toast', {
            detail: { type, message }
        }));
    } else {
        console.warn('[toast] Unknown type:', type);
    }
};

window.toast.success = (msg) => window.toast('success', msg);
window.toast.error   = (msg) => window.toast('error', msg);
window.toast.warning = (msg) => window.toast('warning', msg);
window.toast.info    = (msg) => window.toast('info', msg);

/**
 * Global confirm dialog for HTML forms (non-Livewire).
 * Replaces Swal.fire({showCancelButton: true}) for delete confirmations.
 * Usage:
 *   window.confirmDelete('¿Eliminar cliente?', 'Esta acción no puede deshacerse', '/clients/1', 'delete-client-1');
 *   window.confirmDelete('¿Eliminar?', null, formId);
 */
window.confirmDelete = function (title, message, action, formId) {
    const dialog = document.getElementById('global-confirm-dialog');
    if (!dialog) {
        // Fallback if dialog not yet rendered — force a manual confirm
        if (confirm(title + (message ? '\n\n' + message : ''))) {
            submitForm(action, formId);
        }
        return;
    }

    const alpineData = Alpine.$data(dialog);
    alpineData.title = title;
    alpineData.message = message || '';
    alpineData.action = action;
    alpineData.formId = formId;
    alpineData.open = true;
};

/**
 * Global confirm dialog for Livewire actions (with callback).
 * Usage:
 *   window.confirmAction('¿Generar cuotas?', 'Creará cuotas para el mes actual.', () => Livewire.dispatch('execute-save'));
 */
window.confirmAction = function (title, message, callback) {
    const dialog = document.getElementById('global-confirm-dialog');
    if (!dialog) {
        if (confirm(title + (message ? '\n\n' + message : ''))) {
            callback && callback();
        }
        return;
    }

    const alpineData = Alpine.$data(dialog);
    alpineData.title = title;
    alpineData.message = message || '';
    alpineData.action = '';
    alpineData.formId = '';
    alpineData.open = true;

    // Override confirm to call the callback instead of form submit
    alpineData.confirm = function () {
        this.loading = true;
        this.open = false;
        this.loading = false;
        if (callback) callback();
    };
};

function submitForm(action, formId) {
    if (formId) {
        const form = document.getElementById(formId);
        if (form) form.submit();
    } else if (action) {
        // If action is a URL, create a form and submit it
        const form = document.createElement('form');
        form.method = 'POST';
        form.action = action;
        form.style.display = 'none';

        const csrf = document.querySelector('meta[name="csrf-token"]');
        if (csrf) {
            const token = document.createElement('input');
            token.type = 'hidden';
            token.name = '_token';
            token.value = csrf.getAttribute('content');
            form.appendChild(token);
        }

        const method = document.createElement('input');
        method.type = 'hidden';
        method.name = '_method';
        method.value = 'DELETE';
        form.appendChild(method);

        document.body.appendChild(form);
        form.submit();
    }
}
