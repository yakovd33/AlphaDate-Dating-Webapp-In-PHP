<?php
    require_once('includes/config.php');
    require_once('includes/functions.php');

    if (isset($_POST['email']) || isset($_POST['user_id'])) {
        if (isset($_POST['email'])) {
            $email = $_POST['email'];
            $user = $GLOBALS['link']->query("SELECT * FROM `users` WHERE `email` = '{$email}'")->fetch();
        } else {
            $user = get_user_row_by_id($_POST['user_id']);
        }
        
        $token = new_password_reset_token($user['id']);
        $reset_url = $URL . '/reset/' . $token . '/';
        send_email_to_user($user['id'], 'בקשה לשחזור סיסמא - אלפא דייט', 'על מנת לשחזר סיסמא יש להכנס לקישור הבא: <br> <a href="' . $reset_url . '">לחץ כאן לשחזור סיסמא</a>');
    }
?>