<?php
    if (isset($_POST['email'], $_POST['password'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];
    $user_id = get_user_id_by_email($email);
        $redirect = $URL;

        // Add login attempt row
        if ($GLOBALS['link']->query("SELECT * FROM `login_attempts` WHERE `user_id` = {$user_id} AND `time` >= DATE_SUB(NOW(),INTERVAL 30 MINUTE) AND NOT `is_successful`")->rowCount() < $MAX_LOGIN_ATTEMPTS_PER_HALF_HOUR) {
            $GLOBALS['link']->query("INSERT INTO `login_attempts`(`user_id`) VALUES ({$user_id})");

            if (do_email_and_pass_match($email, $password)) {
                if (check_csrf()) {
                    die('CSRF DETECTED!');
                }

                $_SESSION['user_id'] = $user_id;
                $GLOBALS['link']->query("UPDATE `login_attempts` SET `is_successful` = 1 WHERE `user_id` = {$user_id} ORDER BY `time` DESC LIMIT 1");
                
                // Set cookie
                setcookie('login_hash', get_login_hash(), time() + (86400 * 30), "/");
            } else {
                $redirect = $URL . '?wrong_login';
            }
        } else {
            $redirect = $URL . '?tried_too_much';
        }
    }

    header('Location: ' . $redirect);
?>