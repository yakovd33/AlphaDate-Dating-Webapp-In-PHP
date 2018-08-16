<?php
    require_once('includes/config.php');
    require_once('includes/functions.php');

    if (date("h:i") == '23:59' || 1) {
        $users_stmt = $GLOBALS['link']->query("SELECT * FROM `users` WHERE NOT `banned`");

        while ($user = $users_stmt->fetch()) {
            $days_connected_in_row = 0;
            $days_disconnected_in_row = 0;
            $userid = $user['id'];
            $reward = 0;

            for ($i = 0; $i <= 30; $i++) {
                if ($GLOBALS['link']->query("SELECT * FROM `users_connected_days` WHERE `user_id` = {$userid} AND `date` = DATE(DATE_SUB(NOW(), INTERVAL {$i} DAY))")->rowCount() > 0) {
                    $days_connected_in_row++;
                } else {
                    break;
                }

                if ($GLOBALS['link']->query("SELECT * FROM `users_connected_days` WHERE `user_id` = {$userid} AND `date` = DATE(DATE_SUB(NOW(), INTERVAL {$i} DAY))")->rowCount() == 0) {
                    $days_disconnected_in_row++;
                } else {
                    break;
                }
            }

            switch ($days_connected_in_row) {
                case 7 :
                    $reward = -5;
                case 14 :
                    $reward = -10;
                case 30 :
                    $reward = -25;
                case 60 :
                    $reward = -70;
                case 90 :
                    $reward = - ($user['popularity']);
            }

            switch ($days_connected_in_row) {
                case 1 :
                    $reward = 1;
                case 4 :
                    $reward = 3;
                case 7 :
                    $reward = 6;
                case 10 :
                    $reward = 10;
                case 15 :
                    $reward = 17;
            }

            if ($days_connected_in_row >= 20) {
                $reward = $days_connected_in_row + 8;
            }

            increase_user_popularity($userid, $reward);
        }
    }
?>