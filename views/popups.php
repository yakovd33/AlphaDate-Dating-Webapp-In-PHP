<link rel="stylesheet" href="css/popups.css">
<div id="popups-wrap">
    <?php if (!is_logged()): ?>
        <?php include 'popups/membership.php'; ?>
    <?php endif; ?>
</div>

<script src="js/popups.js"></script>