<div id="confirm-modal-backdrop" class="confirm-modal-backdrop" onclick="if (event.target === this) cancelConfirmAction();">
    <div class="confirm-modal" role="alertdialog" aria-modal="true" aria-labelledby="confirm-modal-message">
        <p id="confirm-modal-message"></p>
        <div class="confirm-modal-actions">
            <button type="button" class="btn btn-secondary" onclick="cancelConfirmAction()">Cancel</button>
            <button type="button" class="btn btn-danger" onclick="acceptConfirmAction()">Confirm</button>
        </div>
    </div>
</div>

<style>
    .confirm-modal-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(10, 10, 10, 0.55);
        z-index: 1000;
        align-items: center;
        justify-content: center;
        padding: 1rem;
    }
    .confirm-modal-backdrop.open {
        display: flex;
    }
    .confirm-modal {
        background: var(--card-bg);
        color: var(--ink);
        border: 2px solid var(--ink);
        box-shadow: var(--shadow);
        padding: 1.5rem;
        width: 100%;
        max-width: 380px;
    }
    .confirm-modal p {
        margin: 0 0 1.25rem;
        font-size: 0.9rem;
        line-height: 1.4;
    }
    .confirm-modal-actions {
        display: flex;
        justify-content: flex-end;
        gap: 0.6rem;
    }
</style>

<script>
    var _confirmActionForm = null;

    function confirmAction(event, message) {
        event.preventDefault();
        _confirmActionForm = event.target;
        document.getElementById('confirm-modal-message').textContent = message;
        document.getElementById('confirm-modal-backdrop').classList.add('open');
        return false;
    }

    function acceptConfirmAction() {
        var form = _confirmActionForm;
        document.getElementById('confirm-modal-backdrop').classList.remove('open');
        _confirmActionForm = null;
        if (form) {
            form.submit();
        }
    }

    function cancelConfirmAction() {
        document.getElementById('confirm-modal-backdrop').classList.remove('open');
        _confirmActionForm = null;
    }

    document.addEventListener('keydown', function (e) {
        if (e.key === 'Escape') cancelConfirmAction();
    });
</script>
