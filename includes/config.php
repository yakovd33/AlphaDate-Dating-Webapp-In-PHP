<?php
    session_start();
    
    $GLOBALS['link'] = new PDO("mysql:host=localhost;dbname=alpha_date;charset=utf8", "root", "");
    $URL = '/AlphaDate';
    define('LOGGED_INTERVAL', 20); // Hashing complexity

    // Templating engine
    require_once(dirname(__DIR__) . '../templates.php');
    require_once(dirname(__DIR__) . '../vendor/autoload.php');
?>