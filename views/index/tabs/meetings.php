<?php
    // Update dates seen status
    if (!isset($_GET['type'])) {
        $GLOBALS['link']->query("UPDATE `meetings_requests` SET `user_one_seen` = 1 WHERE `user_one_id` =  {$_SESSION['user_id']}");
        $GLOBALS['link']->query("UPDATE `meetings_requests` SET `user_two_seen` = 1 WHERE `user_two_id` =  {$_SESSION['user_id']}");

        $meetings_query = "SELECT * FROM `meetings_requests` WHERE ((`user_one_id` = {$_SESSION['user_id']}) OR (`user_two_id` = {$_SESSION['user_id']})) AND `date` > DATE_SUB(NOW(), INTERVAL 1 MONTH) AND NOT `is_approved` AND NOT `is_rejected`";
    } elseif ($_GET['type'] == 'my') {
        $meetings_query = "SELECT * FROM `meetings_requests` WHERE (`user_one_id` = {$_SESSION['user_id']})";
    } elseif ($_GET['type'] == 'memories') {
        $meetings_query = "SELECT * FROM `meetings_requests` WHERE ((`user_one_id` = {$_SESSION['user_id']}) OR (`user_two_id` = {$_SESSION['user_id']})) AND `date` > DATE_SUB(NOW(), INTERVAL 1 MONTH) AND `is_approved`";
    }

    $meetings_query .= get_user_blocked_user_by_col('user_one_id');

    $meetings_stmt = $GLOBALS['link']->query($meetings_query);
?>

<link rel="stylesheet" href="<?php echo $URL; ?>/css/meetings.css">

<a href="<?php echo $URL; ?>/meetings/" class="meeting-filter-link <?php if (!isset($_GET['type'])) { echo 'active'; } ?>">הצעות חדשות</a>
<a href="<?php echo $URL; ?>/meetings/my/" class="meeting-filter-link <?php if (isset($_GET['type']) && $_GET['type'] == 'my') { echo 'active'; } ?>">הצעות ששלחתי</a>
<a href="<?php echo $URL; ?>/meetings/memories/" class="meeting-filter-link <?php if (isset($_GET['type']) && $_GET['type'] == 'memories') { echo 'active'; } ?>">פגישות שאושרו</a>

<div id="meetings-wrap" class="row">
    <?php while ($meeting = $meetings_stmt->fetch()) : ?>
        <?php $meeting_user_id = $meeting['user_one_id'] == $_SESSION['user_id'] ? $meeting['user_two_id'] : $meeting['user_one_id']; ?>
        <?php $profile = get_user_row_by_id($meeting_user_id); ?>
        <div class="profiles-tab-profile-wrap small col-md-3">
            <div class="profiles-tab-profile-card">
                <a href="<?php echo $URL; ?>/profile/<?php echo $profile['id']; ?>/">
                    <div class="pp" style="background-image: url(<?php echo get_user_pp_by_id($profile['id']); ?>);">
                        <img src="<?php echo get_user_pp_by_id($profile['id']); ?>" style="visibility: hidden">
                        <div class="send-message-btn chatbox-trigger" data-userid="<?php echo $profile['id']; ?>"><i class="fas fa-comment-alt"></i></div>
                    </div>
                </a>

                <div class="textual">
                    <a href="<?php echo $URL; ?>/profile/<?php echo $profile['id']; ?>/">
                        <div class="fullname"><?php echo $profile['fullname']; ?></div>
                    </a>

                    <div class="location"><?php echo $profile['city']; ?> | <span class="meeting-request-date">לפני 4 שעות</span></div>
                </div>

                <?php if ($meeting['user_two_id'] == $_SESSION['user_id']) : ?>
                    <?php if (!$meeting['is_approved'] && !$meeting['is_rejected']) : ?>
                        <div class="meeting-actions">
                            <div class="meeting-action approve-date" data-dateid="<?php echo     $meeting['id']; ?>"></div>
                            <div class="meeting-action reject-date" data-dateid="<?php echo  $meeting['id']; ?>"></div>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<script src="<?php echo $URL; ?>/js/meetings.js"></script>