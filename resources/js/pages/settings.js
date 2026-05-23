document.querySelectorAll('.card .flex input[type="text"], .card .flex input[type="email"]').forEach(input => {
    const originalValue = input.value;
    const btn = input.closest('.flex').querySelector('button');
    if (!btn) return;
    input.addEventListener('input', () => {
        btn.style.opacity = input.value !== originalValue ? '1' : '0';
    });
});

const deleteInput = document.getElementById('delete_confirm_input');
const deleteBtn   = document.getElementById('btn_delete_final');
if (deleteInput && deleteBtn) {
    deleteInput.addEventListener('input', () => {
        deleteBtn.disabled = deleteInput.value.trim() !== 'ELIMINA';
    });
}
