<!-- Footer Partial -->
<script src="<?= asset('js/dashboard.js') ?>?v=<?= time() ?>"></script>
<?php if (strpos($path ?? '', '/customer/search') !== false): ?>
    <script src="<?= asset('js/customer.js') ?>"></script>
<?php endif; ?>
</body>

</html>