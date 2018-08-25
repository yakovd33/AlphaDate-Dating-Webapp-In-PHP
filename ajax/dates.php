<?php
    require_once('../includes/config.php');
    require_once('../includes/functions.php');
    
    if (isset($_GET['type'])) {
        switch ($_GET['type']) {
            case 'invite' :
                if (isset($_POST['userid'])) {
                    $id = $_POST['userid'];

                    // Check if not already invited in last 30 days
                    if ($GLOBALS['link']->query("SELECT * FROM `meetings_requests` WHERE `user_one_id` = {$_SESSION['user_id']} AND `user_two_id` = {$id} AND `date` > DATE_SUB(NOW(), INTERVAL 1 MONTH) AND")->rowCount() == 0) {
                        $GLOBALS['link']->query("INSERT INTO `meetings_requests`(`user_one_id`, `user_two_id`) VALUES ({$_SESSION['user_id']}, {$id})");
                    
                        // Take 5 flowers from inviter
                        $GLOBALS['link']->query("UPDATE `users` SET `flowers` = `flowers` - 5 WHERE `id` = {$_SESSION['user_id']}");
                    }
                }

                break;
            case 'approve' :
                if (isset($_POST['dateid'])) {
                    $id = $_POST['dateid'];
                    $date = $GLOBALS['link']->query("SELECT * FROM `meetings_requests` WHERE `id` = {$id}")->fetch();

                    if ($date['user_two_id'] == $_SESSION['user_id']) {
                        $GLOBALS['link']->query("UPDATE `meetings_requests` SET `is_approved` = 1 WHERE `id` = {$id}");
                    }
                }

                break;
            case 'reject' :
                if (isset($_POST['dateid'])) {
                    $id = $_POST['dateid'];
                    $date = $GLOBALS['link']->query("SELECT * FROM `meetings_requests` WHERE `id` = {$id}")->fetch();

                    if ($date['user_two_id'] == $_SESSION['user_id']) {
                        $GLOBALS['link']->query("UPDATE `meetings_requests` SET `is_rejected` = 1 WHERE `id` = {$id}");
                    }
                }

                break;
        }
    }
?>