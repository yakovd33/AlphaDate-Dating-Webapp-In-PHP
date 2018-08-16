<?php
    $matches_query = "SELECT * FROM `hot_or_not_matches` WHERE ((`user_one_id` = {$_SESSION['user_id']} AND NOT `user_one_seen`) OR (`user_two_id` = {$_SESSION['user_id']} AND NOT `user_two_seen`)) OR (`user_one_id` = {$_SESSION['user_id']} OR `user_two_id` = {$_SESSION['user_id']} AND `date` > DATE_SUB(NOW(), INTERVAL 1 MONTH))";
    $matches_query .= get_user_blocked_user_by_col('user_one_id');
    $matches_query .= " ORDER BY `date` DESC";
    $matches_stmt = $GLOBALS['link']->query($matches_query);

    // Update seen
    $GLOBALS['link']->query("UPDATE `hot_or_not_matches` SET `user_one_seen` = 1 WHERE `user_one_id` = {$_SESSION['user_id']} AND NOT `user_one_seen`");
    $GLOBALS['link']->query("UPDATE `hot_or_not_matches` SET `user_two_seen` = 1 WHERE `user_two_id` = {$_SESSION['user_id']} AND NOT `user_two_seen`");
?>

<a href="#" class="matches-type-link <?php if (!isset($_GET['all'])) { echo 'active'; } ?>">התאמות חדשות</a> |
<a href="#" class="matches-type-link <?php if (isset($_GET['all'])) { echo 'active'; } ?>">כל ההתאמות</a>

<div id="matches-wrap">
    <?php while ($match = $matches_stmt->fetch()) : ?>
        <?php $match_user_id = $match['user_one_id'] == $_SESSION['user_id'] ? $match['user_two_id'] : $match['user_one_id']; ?>
        <?php $profile = get_user_row_by_id($match_user_id); ?>
        <div class="profiles-tab-profile-wrap">
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

                    <a href="<?php echo $URL; ?>/city/<?php echo $profile['city']; ?>/">
                        <div class="location"><?php echo $profile['city']; ?></div>
                    </a>
                </div>
            </div>
        </div>
    <?php endwhile; ?>
</div>

<link rel="stylesheet" href="<?php echo $URL; ?>/css/matches.css">