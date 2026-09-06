

import Alpine from '@alpinejs/csp';

window.Alpine = Alpine;

Alpine.data('sidebar', () => ({ sidebarOpen: false }));
Alpine.data('dropdown', () => ({ open: false }));
Alpine.data('passwordInput', () => ({ show: false }));
Alpine.data('submissionForm', () => ({ isSubmitting: false }));
Alpine.data('bookingDetails', () => ({
    showForm: false,
    init() {
        this.showForm = this.$el.dataset.showForm === 'true';
    },
}));
Alpine.data('flashMessage', () => ({
    show: true,
    init() {
        window.setTimeout(() => { this.show = false; }, 2000);
    },
}));
Alpine.data('caseSubmission', () => ({
    isSubmitting: false,
    dokumenList: [{ id: Date.now(), fileName: '', fileSize: '' }],
    addDokumen() {
        if (this.dokumenList.length < 5) {
            this.dokumenList.push({ id: Date.now(), fileName: '', fileSize: '' });
        }
    },
    removeDokumen(index) {
        if (this.dokumenList.length > 1) {
            this.dokumenList.splice(index, 1);
        }
    },
    updateFileName(input, index) {
        const selectedFile = input.files?.[0];
        this.dokumenList[index].fileName = selectedFile?.name ?? '';
        this.dokumenList[index].fileSize = selectedFile
            ? `${(selectedFile.size / (1024 * 1024)).toFixed(2)} MB`
            : '';
    },
}));
Alpine.data('verificationForm', () => ({
    statusVerifikasi: 'berkas_lengkap',
    docStatus: {},
    isSubmitting: false,
    init() {
        this.statusVerifikasi = this.$el.dataset.initialStatus;
        this.docStatus = JSON.parse(this.$el.dataset.documentStatuses ?? '{}');
    },
    setToLengkap() {
        this.statusVerifikasi = 'berkas_lengkap';
        Object.keys(this.docStatus).forEach((id) => { this.docStatus[id] = 'valid'; });
    },
    setDocumentStatus(event) {
        this.docStatus[event.target.dataset.documentId] = event.target.value;
    },
}));
Alpine.data('modal', () => ({
    show: false,
    init() {
        this.show = this.$el.dataset.initialShow === 'true';
        this.$watch('show', (value) => {
            document.body.classList.toggle('overflow-y-hidden', value);
            if (value && this.$el.dataset.focusable === 'true') {
                window.setTimeout(() => this.firstFocusable()?.focus(), 100);
            }
        });
    },
    focusables() {
        const selector = 'a, button, input:not([type="hidden"]), textarea, select, details, [tabindex]:not([tabindex="-1"])';
        return [...this.$el.querySelectorAll(selector)].filter((element) => !element.disabled);
    },
    firstFocusable() { return this.focusables()[0]; },
    lastFocusable() { return this.focusables().at(-1); },
    nextFocusable() {
        const items = this.focusables();
        return items[(items.indexOf(document.activeElement) + 1) % items.length] ?? items[0];
    },
    previousFocusable() {
        const items = this.focusables();
        const index = Math.max(0, items.indexOf(document.activeElement) - 1);
        return items[index] ?? items.at(-1);
    },
    open(event) {
        if (event.detail === this.$el.dataset.modalName) this.show = true;
    },
    closeFor(event) {
        if (event.detail === this.$el.dataset.modalName) this.show = false;
    },
    focusNext(event) {
        const target = event.shiftKey ? this.previousFocusable() : this.nextFocusable();
        target?.focus();
    },
    close() { this.show = false; },
}));

Alpine.start();

document.addEventListener('DOMContentLoaded', () => {
    document.querySelector('[data-scroll-error]')?.scrollIntoView({ behavior: 'smooth', block: 'start' });

    document.querySelectorAll('[data-confirm]').forEach((element) => {
        const eventName = element instanceof HTMLFormElement ? 'submit' : 'click';
        element.addEventListener(eventName, (event) => {
            if (!window.confirm(element.dataset.confirm)) {
                event.preventDefault();
            }
        });
    });

    document.querySelectorAll('[data-auto-submit]').forEach((element) => {
        element.addEventListener('change', () => element.form?.requestSubmit());
    });

    document.querySelectorAll('[data-submit-parent-form]').forEach((element) => {
        element.addEventListener('click', (event) => {
            event.preventDefault();
            element.closest('form')?.requestSubmit();
        });
    });

    document.querySelectorAll('[data-submit-form]').forEach((element) => {
        element.addEventListener('click', () => {
            const source = document.getElementById(element.dataset.copyFrom ?? '');
            const target = document.getElementById(element.dataset.copyTo ?? '');
            const form = document.getElementById(element.dataset.submitForm ?? '');

            if (source && target) {
                target.value = source.value;
            }

            form?.requestSubmit();
        });
    });

    document.querySelectorAll('[data-file-input]').forEach((input) => {
        input.addEventListener('change', () => {
            const display = document.getElementById(input.dataset.fileDisplay ?? '');
            const instruction = document.getElementById(input.dataset.fileInstruction ?? '');
            const selectedFile = input.files?.[0];

            if (display) {
                display.textContent = selectedFile?.name ?? '';
                display.classList.toggle('hidden', !selectedFile);
            }

            if (instruction) {
                instruction.textContent = selectedFile ? 'File dipilih:' : 'Pilih file untuk diunggah';
            }
        });
    });

    document.querySelectorAll('[data-window-close]').forEach((button) => {
        button.addEventListener('click', () => window.close());
    });

    document.querySelectorAll('[data-window-print]').forEach((button) => {
        button.addEventListener('click', () => window.print());
    });

    if (document.body.hasAttribute('data-auto-print')) {
        window.setTimeout(() => window.print(), 500);
    }
});
