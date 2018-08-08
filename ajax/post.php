<?php
    require_once('../includes/config.php');
    require_once('../includes/functions.php');
    
    if (isset($_GET['type'])) {
        switch ($_GET['type']) {
            case 'upload' :
                // Post upload
                $text = trim(addslashes(htmlentities($_POST['text'])));
                $photo = null;

                $GLOBALS['link']->query("INSERT INTO `posts`(`user_id`, `text`, `photo_url`) VALUES ({$_SESSION['user_id']}, '{$text}', '{$photo}')");

                $resp = [];
                $resp['postid'] = $GLOBALS['link']->lastInsertId();

                echo json_encode($resp);

                break;
            case 'heart' :
                // Post heart
                if (isset($_GET['post_id'])) {
                    $post_id = $_GET['post_id'];
                    $hearted = $GLOBALS['link']->query("SELECT * FROM `posts_hearts` WHERE `post_id` = {$post_id} AND `user_id` = {$_SESSION['user_id']}")->rowCount() > 0;
                    
                    if ($hearted) {
                        // Delete heart
                        $GLOBALS['link']->query("DELETE FROM `posts_hearts` WHERE `post_id` = {$post_id} AND `user_id` = {$_SESSION['user_id']}");
                    } else {
                        // Insert heart
                        $GLOBALS['link']->query("INSERT INTO `posts_hearts`(`post_id`, `user_id`) VALUES ({$post_id}, {$_SESSION['user_id']})");
                    }
                }
                break;
            case 'comment' :
                if (isset($_POST['postid'], $_POST['comment'])) {
                    $resp = [];
                    
                    $postid = $_POST['postid'];
                    $comment = trim(addslashes(htmlentities($_POST['comment'])));
                    $comment_user = get_user_row_by_id($_SESSION['user_id']);
                    $resp['fullname'] = $comment_user['fullname'];
                    $resp['pp'] = get_user_pp_by_id($_SESSION['user_id']);
                    $resp['comment'] = $comment;

                    // Insert comment
                    $GLOBALS['link']->query("INSERT INTO `posts_comments`(`post_id`, `user_id`, `comment`) VALUES ({$postid}, {$_SESSION['user_id']}, '{$comment}')");
                    echo json_encode($resp);
                }

                break;
            case 'get_comments' :
                if (isset($_POST['postid'])) {
                    $postid = $_POST['postid'];
                    $comments_stmt = $GLOBALS['link']->query("SELECT * FROM `posts_comments` WHERE `post_id` = {$postid} ORDER BY `date` DESC");
                    $comments = [];

                    while ($comment = $comments_stmt->fetch()) {
                        $comment_user = get_user_row_by_id($comment['user_id']);
                        $comment['fullname'] = $comment_user['fullname'];
                        $comment['pp'] = get_user_pp_by_id($comment['user_id']);

                        array_push($comments, $comment);
                    }

                    echo json_encode($comments);
                }

                break;
        }
    }
?>