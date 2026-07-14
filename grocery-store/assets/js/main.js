// Quantity input controls
document.querySelectorAll('.qty-decrease').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = btn.nextElementSibling;
        if (parseInt(input.value) > 1) input.value = parseInt(input.value) - 1;
    });
});
document.querySelectorAll('.qty-increase').forEach(btn => {
    btn.addEventListener('click', () => {
        const input = btn.previousElementSibling;
        input.value = parseInt(input.value) + 1;
    });
});

// Auto-dismiss alerts
setTimeout(() => {
    document.querySelectorAll('.alert.fade').forEach(el => {
        const bsAlert = bootstrap.Alert.getOrCreateInstance(el);
        bsAlert.close();
    });
}, 4000);
