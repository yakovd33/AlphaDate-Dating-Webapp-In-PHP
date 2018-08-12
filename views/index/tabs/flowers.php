<?php
    $new_flowers_query = "SELECT * FROM `sent_flowers` WHERE `to_id` = {$_SESSION['user_id']} AND NOT `seen` ORDER BY `date` DESC";
    $new_flowers_query .= get_user_blocked_user_by_col('from_id');
    $new_flowers_stmt = $GLOBALS['link']->query($new_flowers_query);
?>

<div id="flowers">
    <?php if ($new_flowers_stmt->rowCount() == 0) : ?>
        <div class="card">
            אף אחד לא שלח לך פרחים.
        </div>
    <?php endif; ?>

    <?php while ($flower = $new_flowers_stmt->fetch()) : ?>
        <?php $sender = get_user_row_by_id($flower['from_id']); ?>
        <div class="flower-wrap card">
            <div class="text">
                קיבלת פרח מאת <a href="<?php echo $URL; ?>/profile/<?php echo $sender['id']; ?>/"><?php echo $sender['fullname']; ?></a>
            </div>

            <div class="date"><?php echo friendly_time($flower['date']); ?></div>
        </div>
    <?php endwhile; ?>
</div>