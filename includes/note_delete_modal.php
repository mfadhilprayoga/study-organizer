<div class="note-delete-modal" id="note-delete-modal" hidden>
    <section class="note-delete-dialog" role="dialog" aria-modal="true" aria-labelledby="note-delete-heading" aria-describedby="note-delete-message" tabindex="-1">
        <span class="note-delete-icon" aria-hidden="true">
            <svg viewBox="0 0 24 24"><path d="M4 7h16M10 11v6m4-6v6M5.5 7l1 13h11l1-13M9 7V4h6v3"/></svg>
        </span>
        <h2 id="note-delete-heading">Hapus catatan?</h2>
        <p id="note-delete-message">Tindakan ini tidak dapat dibatalkan. Catatan berikut akan dihapus:</p>
        <p class="note-delete-title" id="note-delete-title"></p>
        <form class="note-delete-form" action="hapus.php" method="POST">
            <?= csrf_field() ?>
            <div class="note-delete-actions">
                <button class="note-delete-cancel" type="button" data-close-delete-modal>Batal</button>
                <button class="note-delete-confirm" type="submit">Hapus Catatan</button>
            </div>
        </form>
    </section>
</div>

<script>
(() => {
    const modal = document.getElementById('note-delete-modal');
    const form = modal.querySelector('.note-delete-form');
    const title = modal.querySelector('#note-delete-title');
    const cancelButton = modal.querySelector('[data-close-delete-modal]');
    let activeTrigger = null;

    const closeModal = () => {
        modal.hidden = true;
        document.body.classList.remove('note-delete-modal-open');
        if (activeTrigger) {
            activeTrigger.focus();
            activeTrigger = null;
        }
    };

    document.addEventListener('click', (event) => {
        const trigger = event.target.closest('.note-delete-trigger');
        if (trigger) {
            event.preventDefault();
            activeTrigger = trigger;
            form.action = trigger.href;
            title.textContent = trigger.dataset.noteTitle;
            modal.hidden = false;
            document.body.classList.add('note-delete-modal-open');
            cancelButton.focus();
            return;
        }

        if (event.target === modal || event.target.closest('[data-close-delete-modal]')) {
            closeModal();
        }
    });

    document.addEventListener('keydown', (event) => {
        if (modal.hidden) {
            return;
        }

        if (event.key === 'Escape') {
            event.preventDefault();
            closeModal();
        } else if (event.key === 'Tab') {
            const focusable = [cancelButton, form.querySelector('[type="submit"]')];
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
    });
})();
</script>
