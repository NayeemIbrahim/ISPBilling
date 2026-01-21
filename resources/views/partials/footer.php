<!-- Footer Partial -->
<script src="<?= asset('js/dashboard.js') ?>?v=<?= time() ?>"></script>
<?php if (strpos($path ?? '', '/customer/search') !== false): ?>
    <script src="<?= asset('js/customer.js') ?>"></script>
<?php endif; ?>
<!-- Flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr(".date-picker", {
            dateFormat: "d/m/Y",
            altInput: true,
            altFormat: "Y/m/d",
            allowInput: true,
            placeholder: "DD/MM/YYYY"
        });
    });
</script>
</body>

</html>