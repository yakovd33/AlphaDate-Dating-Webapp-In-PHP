<?php
    require_once('includes/config.php');
    require_once('includes/functions.php');

    $fb = new Facebook\Facebook([
        'app_id' => '1413642492113699',
        'app_secret' => 'd789743df2ef744ce3e8546adf060934',
        'default_graph_version' => 'v2.8',
    ]);

    $helper = $fb->getRedirectLoginHelper();
    // $login_url = $helper->getLoginUrl($URL . "/fb-login/");
    $login_url = $helper->getLoginUrl('http://localhost:8080/' . $URL . "/fb-login/");
?>