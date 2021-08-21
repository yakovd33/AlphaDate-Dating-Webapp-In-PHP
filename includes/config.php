<?php
    session_start();
    $IS_DEV = true;
    
    if ($IS_DEV) {
        $GLOBALS['link'] = new PDO("mysql:host=localhost;dbname=alpha_date;charset=utf8", "root", "");
    } else {
        $GLOBALS['link'] = new PDO("mysql:host=localhost;dbname=alphadat_alphadate;charset=utf8", "alphadat_alphadate", "aEP;6&@XvHdi");
    }

    $URL = '/AlphaDate';
    $ADMIN_URL = $URL . '/admin';
    
    $GLOBALS['url'] = $URL;
    $GLOBALS['admin_url'] = $ADMIN_URL;
    define('LOGGED_INTERVAL', 20); // Hashing complexity

    // Templating engine
    if ($IS_DEV) {
        require_once(dirname(__DIR__) . '../templates.php');
        require_once(dirname(__DIR__) . '../vendor/autoload.php');
    } else {
        require_once('templates.php');
        require_once('vendor/autoload.php');
    }

    $MAX_LOGIN_ATTEMPTS_PER_HALF_HOUR = 5;
?>