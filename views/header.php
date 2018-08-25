<!DOCTYPE html>
<html lang="en" dir="rtl">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Alpha Date</title>
        <script src="<?php echo $URL; ?>/js/handlebars-v4.0.11.js"></script>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
        <link rel="stylesheet" href="<?php echo $URL; ?>/css/main.css">

        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <script src="https://code.jquery.com/ui/1.12.1/jquery-ui.min.js" integrity="sha256-VazP97ZCwtekAsvgPBSUwPFKdrwD3unUfSGVYrahUqU=" crossorigin="anonymous"></script>
        <script type="text/javascript" src="//cdnjs.cloudflare.com/ajax/libs/jqueryui-touch-punch/0.2.3/jquery.ui.touch-punch.min.js"></script>

        <!-- Slick JS -->
        <link rel="stylesheet" type="text/css" href="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
        <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
        <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/js/bootstrap.min.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/toastr.js/latest/toastr.min.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/socket.io/2.1.1/socket.io.js"></script>

        <script src="<?php echo $URL; ?>/js/functions.js"></script>
    </head>
    <body>
    <!-- <div style="width: 300px; height: 472px !important; background: #fcb555; z-index: 999; position: fixed; bottom: -200px; left: -200px; transform: rotate(130deg);"><div class="text"></div></div> -->
        <?php if (is_logged()) : ?>
            <input type="hidden" id="url" value="<?php echo $URL; ?>">
            <input type="hidden" id="userid" value="<?php echo $_SESSION['user_id']; ?>">
            <input type="hidden" id="userid" value="<?php echo $_SESSION['user_id']; ?>">
            <input type="hidden" id="fullname" value="<?php echo $CUR_USER['fullname']; ?>">
            <input type="hidden" id="pp" value="<?php echo get_user_pp_by_id($CUR_USER['id']); ?>">
            <input type="hidden" id="csrf_token" value="<?php echo $_SESSION['csrf_token'] = md5(time() + rand(0, 100)); ?>">
        <?php endif; ?>

        <script>
            URL = $("#url").val();
            USERID = $("#userid").val();
            FULLNAME = $("#fullname").val();
            PP = $("#pp").val();
            isMobileFloatingChat = <?php echo $CUR_USER['mobile_floating_stories']; ?>;
            current_unread_messages = <?php echo get_num_unread_messages(); ?>;
        </script>

        <div id="popups-bg"></div>

        <div id="empty-nav">
            <a href="<?php echo $URL; ?>"><div id="empty-nav-logo"></div></a>

            <div class="container" id="empty-nav-logout-btn-wrap">
                <a href="<?php echo $URL; ?>/logout/"><div id="empty-nav-logout-btn"></div></a>
            </div>

            <div id="empty-nav-mobile-menu-toggler">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>

        <div id="mobile-story">
            <?php
                $recent_stories_users_stmt = $GLOBALS['link']->query("SELECT DISTINCT `user_id` AS `uid` FROM `stories` WHERE `date` > DATE_SUB(NOW(), INTERVAL 1 DAY) AND `user_id` <> {$_SESSION['user_id']} " . get_user_blocked_user_by_col('user_id'));
                
                // Sort stories by date
                $users_last_stories = [];

                while ($story_user = $recent_stories_users_stmt->fetch()) {
                    $uid = $story_user['uid'];
                    $user_last_story = $GLOBALS['link']->query("SELECT * FROM `stories` WHERE `user_id` = {$uid} ORDER BY `date` DESC LIMIT 1")->fetch();
                    array_push($users_last_stories, $user_last_story);
                }

                usort($users_last_stories, 'sort_by_date');
                
                foreach ($users_last_stories as $story) {
                    $story['user_id'] . ' ' . $story['date'] . '<br>';
                }

                $self_story_stmt = $GLOBALS['link']->query("SELECT DISTINCT `user_id` AS `uid` FROM `stories` WHERE `date` > DATE_SUB(NOW(), INTERVAL 1 DAY) AND `user_id` = {$_SESSION['user_id']}");
                
                // Filter seen stories
                $unseen_stories = [];
                $seen_stories = [];

                foreach ($users_last_stories as $story) {
                    if (has_user_seen_story($story['id'])) {
                        array_push($seen_stories, $story);
                    } else {
                        array_push($unseen_stories, $story);
                    }
                }
            ?>

            <div id="mobiles-stories-list" class="story-list">
                <div id="sidebar-story-add-btn-mobile">
                    <div class="icon"><i class="fas fa-plus"></i></div>
                </div>

                <?php while ($story = $self_story_stmt->fetch()) : ?>
                    <?php $uid = $_SESSION['user_id']; ?>
                    <?php $story_user = get_user_row_by_id($uid); ?>
                    <?php $user_last_story = $GLOBALS['link']->query("SELECT * FROM `stories` WHERE `user_id` = {$uid} ORDER BY `id` DESC LIMIT 1")->fetch(); ?>
                    
                    <div class="item" data-userid="<?php echo $_SESSION['user_id']; ?>">
                        <div class="pic"><img src="<?php echo get_user_pp_by_id($uid); ?>" alt=""></div>
                        <div class="fullname"><?php echo $story_user['fullname']; ?></div>
                    </div>
                <?php endwhile; ?>

                <?php foreach ($unseen_stories as $story) : ?>
                    <?php $uid = $story['user_id']; ?>
                    <?php $story_user = get_user_row_by_id($uid); ?>
                    <?php $user_last_story = $GLOBALS['link']->query("SELECT * FROM `stories` WHERE `user_id` = {$uid} ORDER BY `id` DESC LIMIT 1")->fetch(); ?>

                    <div class="item" data-userid="<?php echo $uid; ?>">
                        <div class="pic"><img src="<?php echo get_user_pp_by_id($uid); ?>" alt=""></div>
                        <div class="fullname"><?php echo $story_user['fullname']; ?></div>
                    </div>
                <?php endforeach; ?>

                <?php foreach ($seen_stories as $story) : ?>
                    <?php $uid = $story['user_id']; ?>
                    <?php $story_user = get_user_row_by_id($uid); ?>
                    <?php $user_last_story = $GLOBALS['link']->query("SELECT * FROM `stories` WHERE `user_id` = {$uid} ORDER BY `id` DESC LIMIT 1")->fetch(); ?>

                    <div class="item" data-userid="<?php echo $uid; ?>">
                        <div class="pic"><img src="<?php echo get_user_pp_by_id($uid); ?>" alt=""></div>
                        <div class="fullname"><?php echo $story_user['fullname']; ?></div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <?php if (isset($_GET['page']) && $_GET['page'] != 'profile' || !isset($_GET['page'])) : ?>
            <div class="container" id="site-wrap">
        <?php endif; ?>