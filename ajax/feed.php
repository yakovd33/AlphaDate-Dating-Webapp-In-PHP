<?php
    require_once('../includes/config.php');
    require_once('../includes/functions.php');
    
    if (isset($_GET['type'])) {
        switch ($_GET['type']) {
            case 'get-main-feed-page' :
                if (isset($_GET['page'])) {
                    $page = $_GET['page'];
                    $resp = [];
                    $resp['posts'] = [];

                    if (!isset($_POST['userid'])) {
                        $posts_query = "SELECT * FROM `posts` WHERE 1 ";
                        $posts_query .= get_user_blocked_user_by_col('user_id');
                    } else {
                        $userid = $_POST['userid'];

                        // Check if user is not blocked
                        if ($GLOBALS['link']->query("SELECT * FROM `blocked_users` WHERE `user_id` = {$userid} AND `blocked_id` = {$_SESSION['user_id']}")->rowCount() == 0 && !is_user_blocked($userid)) {
                            $posts_query = "SELECT * FROM `posts` WHERE `user_id` = {$userid} ";
                        }
                    }

                    $posts_query .= " ORDER BY `date` DESC LIMIT " . $page * get_setting('posts_per_page') . ', ' . get_setting('posts_per_page');
                    $posts_stmt = $GLOBALS['link']->query($posts_query);

                    while ($post = $posts_stmt->fetch()) {
                        $poster = get_user_row_by_id($post['user_id']);
                        $post_id = $post['id'];
                        $num_hearts = $GLOBALS['link']->query("SELECT * FROM `posts_hearts` WHERE `post_id` = {$post_id}")->rowCount();
                        $num_comments = $GLOBALS['link']->query("SELECT * FROM `posts_comments` WHERE `post_id` = {$post_id}")->rowCount();

                        array_push($resp['posts'], [
                            'postid' => $post_id,
                            'userid' => $post['user_id'],
                            'fullname' => $poster['fullname'],
                            'text' => nl2br($post['text']),
                            'time' => friendly_time($post['date']),
                            'num_hearts' => $num_hearts,
                            'num_comments' => $num_comments,
                            'hearted' => $GLOBALS['link']->query("SELECT * FROM `posts_hearts` WHERE `post_id` = {$post_id} AND `user_id` = {$_SESSION['user_id']}")->rowCount() > 0,
                            'user_pic' => get_user_pp_by_id($post['user_id'])
                        ]);
                    }

                    echo json_encode($resp);
                }

                break;
        }
    }
?>