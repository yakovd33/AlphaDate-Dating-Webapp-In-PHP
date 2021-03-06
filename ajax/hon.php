<?php
    require_once('../includes/config.php');
    require_once('../includes/functions.php');
    require_once('../includes/hor-functions.php');
    
    if (isset($_GET['type'])) {
        switch ($_GET['type']) {
            case 'upload_hon_pic' :
                // Hot or not pic upload

                if (isset($_FILES['pic'])) {
                    // Check if users hon pics is not over 6
                    if (get_user_num_hon_pics($_SESSION['user_id']) < 6) {
                        $image_id = insert_photo($_FILES['pic'], 'hot-or-not-pics', 'hot_or_not');
                        
                        // Insert hot or not pic
                        $GLOBALS['link']->query("INSERT INTO `hot_or_not_pics`(`user_id`, `image_id`) VALUES ({$_SESSION['user_id']}, {$image_id})");
                    }
                }

                break;
            case 'delete_hon_pic' :
                if (isset($_POST['picid'])) {
                    $picid = $_POST['picid'];

                    // Check if current user is the owner of the picture
                    if ($GLOBALS['link']->query("SELECT * FROM `hot_or_not_pics` WHERE `user_id` = {$_SESSION['user_id']} AND `id` = {$picid}")->rowCount() > 0) {
                        $GLOBALS['link']->query("DELETE FROM `hot_or_not_pics` WHERE `id` = {$picid}");
                    }
                }    

                break;
            case 'user_join_hon' :
                if (is_cur_user_profile_complete() && get_user_num_hon_pics($_SESSION['user_id']) > 0) {
                    $GLOBALS['link']->query("UPDATE `users` SET `is_in_hot_or_not` = 1 WHERE `id` = {$_SESSION['user_id']}");
                }
                break;
            case 'get_next_hon' :
                echo get_hon();
                break;
            case 'heart' :
                heart();
                break;
            case 'reject' :
                reject();
                break;
        }
    }
?>