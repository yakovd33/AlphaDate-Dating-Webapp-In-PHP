<?php
    require_once('../includes/config.php');
    require_once('../includes/functions.php');
    
    if (isset($_GET['type'])) {
        switch ($_GET['type']) {
            case 'upload' :
                // Post upload
                $text = $_POST['text'];
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
        }
    }
?>