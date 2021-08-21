<link rel="stylesheet" href="<?php echo $URL; ?>/css/popups.css">
<div id="popups-wrap">
    <?php if (!is_logged()): ?>
        <?php include 'popups/membership.php'; ?>
    <?php endif; ?>
</div>

<script src="<?php echo $URL; ?>/js/popups.js"></script>