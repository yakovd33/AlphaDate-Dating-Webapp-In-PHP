<?php
    require_once('../includes/config.php');
    require_once('../includes/functions.php');
    
    if (isset($_GET['type'])) {
        switch ($_GET['type']) {
            case 'upload' :
                // Post upload
                $text = trim(addslashes(htmlentities($_POST['text'])));
                $photo = null;
                $anonymous = 0;
                $anonymous_nickname = 'אנונימי';

                if (check_csrf()) {
                    die('CSRF DETECTED!');
                }

                if (isset($_FILES['image'])) {
                    $file = $_FILES['image'];
                    $photo = insert_photo($file, 'posts_pics');
                }

                if (!empty($text) || $photo) {
                    if (get_user_row_by_id($_SESSION['user_id'])['is_premium']) {
                        if (isset($_POST['is_anonymous']) && $_POST['is_anonymous'] == 'true') {
                            $anonymous = 1;

                            if (isset($_POST['anonymous_nickname'])) {
                                if (!empty($_POST['anonymous_nickname'])) {
                                    $anonymous_nickname = addslashes(htmlentities($_POST['anonymous_nickname']));
                                }
                            }
                        }
                    }

                    $new_post_prep_stmt = $GLOBALS['link']->prepare("INSERT INTO `posts`(`user_id`, `text`, `image_id`, `is_anonymous`, `anonymous_nickname`) VALUES (?, ?, ?, ?, ?)");
                    $new_post_prep_stmt->execute([$_SESSION['user_id'], $text, $photo, $anonymous, $anonymous_nickname]);
                    
                    $resp = [];
                    $resp['postid'] = $GLOBALS['link']->lastInsertId();

                    echo json_encode($resp);
                }

                break;
            case 'heart' :
                // Post heart
                if (isset($_GET['post_id'])) {
                    $post_id = $_GET['post_id'];
                    $hearted = $GLOBALS['link']->query("SELECT * FROM `posts_hearts` WHERE `post_id` = {$post_id} AND `user_id` = {$_SESSION['user_id']}")->rowCount() > 0;
                    
                    if ($hearted) {
                        // Delete heart
                        $GLOBALS['link']->query("DELETE FROM `posts_hearts` WHERE `post_id` = {$post_id} AND `user_id` = {$_SESSION['user_id']}");
                        
                        $GLOBALS['link']->query("UPDATE `posts` SET `hearts` = `hearts` - 1 WHERE `id` = {$post_id}");
                    } else {
                        // Insert heart
                        $GLOBALS['link']->query("INSERT INTO `posts_hearts`(`post_id`, `user_id`) VALUES ({$post_id}, {$_SESSION['user_id']})");
                        $GLOBALS['link']->query("UPDATE `posts` SET `hearts` = `hearts` + 1 WHERE `id` = {$post_id}");
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
                    $GLOBALS['link']->query("UPDATE `posts` SET `comments` = `comments` + 1 WHERE `id` = {$postid}");
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
            case 'delete' :
                // Post delete
                if (isset($_GET['post_id'])) {
                    $post_id = $_GET['post_id'];
                    $GLOBALS['link']->query("UPDATE `posts` SET `is_deleted` = 1 WHERE `id` = {$post_id} AND `user_id` = {$_SESSION['user_id']}");
                }
                break;
            case 'update' :
                if (isset($_GET['post_id'], $_POST['text'])) {
                    $post_id = $_GET['post_id'];
                    $text = htmlentities($_POST['text']);
                    
                    // Save old version
                    $old_version_stmt = $GLOBALS['link']->query("SELECT * FROM `posts` WHERE `id` = {$post_id} AND `user_id` = {$_SESSION['user_id']}");
                    if ($old_version_stmt->rowCount() > 0) {
                        $old_version = $old_version_stmt->fetch()['text'];

                        if ($text != $old_version) {
                            $GLOBALS['link']->query("INSERT INTO `posts_versions`(`post_id`, `text`) VALUES ({$post_id}, '{$old_version}')");
                            $GLOBALS['link']->query("UPDATE `posts` SET `text` = '{$text}' WHERE `id` = {$post_id} AND `user_id` = {$_SESSION['user_id']}");
                        }
                    }
                }

                break;
        }
    }
?>