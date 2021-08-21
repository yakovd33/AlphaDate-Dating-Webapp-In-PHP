<?php
    $posts_query = "SELECT *, `hearts` + `comments` AS `hotness` FROM `posts` WHERE 1 ";
    $posts_query .= get_user_blocked_user_by_col('user_id');
    $posts_query .= get_banned_user_by_col('user_id');
    $posts_query .= " AND NOT `is_deleted`";

    switch ($CUR_USER['feed_sort']) {
        case 'date' :
            $posts_query .= " ORDER BY `date` DESC";
            break;
        case 'hot' :
            $posts_query .= " AND `date` > DATE_SUB(NOW(), INTERVAL 1 WEEK) ORDER BY `hotness` DESC";
            break;
        case 'is_anonymous' :
            $posts_query .= " AND `is_anonymous` ORDER BY `date` DESC";
            break;
    }

    $posts_query .= " LIMIT " . get_setting('posts_per_page');
    $posts_stmt = $GLOBALS['link']->query($posts_query);

    echo $handlebars->render("new_post", [
        'fullname' => $CUR_USER['fullname'],
        'nickname' => $CUR_USER['nickname'],
        'user_pic' => get_user_pp_by_id($CUR_USER['id'])
    ]);
?>

<script>
    let isMainFeed = true;
    let isProfileFeed = false;
    let feedPage = 0;
    let hasFeedEnded = false;
    let postsPerPage = <?php echo get_setting('posts_per_page'); ?>;
</script>

<div id="feed-sorting-wrap">
    מיין לפי:
    <a href="#" class="feed-sorting-option <?php if ($CUR_USER['feed_sort'] == 'date') { echo 'active'; } ?>" data-sort="date">זמן</a> |
    <a href="#" class="feed-sorting-option <?php if ($CUR_USER['feed_sort'] == 'hot') { echo 'active'; } ?>" data-sort="hot">הכי חם</a> |
    <a href="#" class="feed-sorting-option <?php if ($CUR_USER['feed_sort'] == 'is_anonymous') { echo 'active'; } ?>" data-sort="is_anonymous">אנונימי</a>
</div>

<div id="feed-posts">
    <?php
        while ($post = $posts_stmt->fetch()) {
            $poster = get_user_row_by_id($post['user_id']);
            $post_id = $post['id'];
            $num_hearts = $GLOBALS['link']->query("SELECT * FROM `posts_hearts` WHERE `post_id` = {$post_id}")->rowCount();
            $num_comments = $GLOBALS['link']->query("SELECT * FROM `posts_comments` WHERE `post_id` = {$post_id}")->rowCount();

            $post_userid = $post['user_id'];
            $post_fullname = $poster['fullname'];
            $post_userpic = get_user_pp_by_id($post['user_id']);
            $is_anonymous = '';

            if ($post['is_anonymous']) {
                $post_userid = '';
                $post_fullname = $post['anonymous_nickname'];
                $post_userpic = $URL . '/img/anonymous.png';
                $is_anonymous = 'anonymous';
            }

            $args = [
                'postid' => $post['id'],
                'userid' => $post_userid,
                'profile_hash' => $poster['profile_hash'],
                'fullname' => $post_fullname,
                'text' => nl2br($post['text']),
                'time' => friendly_time($post['date']),
                'num_hearts' => $num_hearts,
                'num_comments' => $num_comments,
                'hearted' => $GLOBALS['link']->query("SELECT * FROM `posts_hearts` WHERE `post_id` = {$post_id} AND `user_id` = {$_SESSION['user_id']}")->rowCount() > 0,
                'user_pic' => $post_userpic,
                'anonymous' => $is_anonymous,
                'isPic' => $post['image_id'] == null ? 'noPic' : 'yesPic',
                'self' => $post['user_id'] == $_SESSION['user_id'] ? 'self' : ''
            ];

            if ($post['image_id']) {
                $args['image'] = $URL . '/' . get_image_path_by_id($post['image_id']);
            }

            echo $handlebars->render("post", $args);
        }
    ?>
</div>

<div id="feed-loader"></div>

<script id="post-template" type="text/x-handlebars-template">
    <?php include 'templates/post.hbs'; ?>
</script>

<script src="<?php echo $URL; ?>/js/feed.js"></script>