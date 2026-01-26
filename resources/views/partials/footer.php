<footer class="main-footer"
    style="padding: 20px; text-align: center; color: #666; font-size: 0.85rem; border-top: 1px solid #eee; margin-top: 40px;">
    <p>&copy; <?= date('Y') ?> HK ISP Billing. All rights reserved. | <a href="<?= url('changelog') ?>"
            style="color: #666; text-decoration: none;">Version <?= APP_VERSION ?></a></p>
</footer>
<script src="<?= asset('js/dashboard.js') ?>?v=<?= time() ?>"></script>
<?php if (strpos($path ?? '', '/customer/search') !== false): ?>
    <script src="<?= asset('js/customer.js') ?>"></script>
<?php endif; ?>
<!-- Flatpickr -->
<script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>
<script>
    document.addEventListener('DOMContentLoaded', function () {
        flatpickr(".date-picker", {
            dateFormat: "Y-m-d", // Server format (Standard)
            altInput: true,
            altFormat: "d/m/Y",  // Display format (User requested)
            allowInput: true,
            placeholder: "DD/MM/YYYY"
        });
    });
</script>
</body>

</html>