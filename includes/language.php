<?php
    if (isset($_GET['language'])) {
        $language = $_GET['language'];
        $GLOBALS['link']->query("UPDATE users SET `language` = '{$language}' WHERE `id` = {$_SESSION['user_id']}");
    } else {
        $language = 'en';
    }

    $DIRECTION = $language == 'en' ? 'ltr' : 'rtl';

    if (is_logged()) {
        $CUR_USER = get_user_row_by_id($_SESSION['user_id']);
        update_last_seen();
        $language = $CUR_USER['language'];
    }

    require_once('languages/' . $language . '.php');
    setcookie('language', $language, time() + (86400 * 30 * 365), "/");
?>