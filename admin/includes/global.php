<?php
    require_once('../includes/config.php');
    require_once('../includes/functions.php');

    if (!is_logged() && isset($_COOKIE['login_hash']) && $_COOKIE['login_hash'] != 0) {
        $hash = $_COOKIE['login_hash'];
        $_SESSION['user_id'] = $GLOBALS['link']->query("SELECT `user_id` FROM `login_hashes` WHERE `hash` = '{$hash}'")->fetch()['user_id'];
    }

    if (is_logged()) {
        $CUR_USER = get_user_row_by_id($_SESSION['user_id']);
        if ($CUR_USER['is_admin']) {
            update_last_seen();
        } else {
            die();
        }
    } else {
        die();
    }
?>