<?php
    require_once('includes/config.php');
    require_once('includes/functions.php');

    $json = file_get_contents('emojis_map.json');
    $emojis = json_decode($json, true);

    // var_dump($emojis);
    foreach ($emojis as $emoji) {
        $x = $emoji['x'];
        $y = $emoji['y'];
        $file = substr_replace($emoji['file'], "", -1);
        $name = $emoji['name'];
        $background_size = $emoji['background_size'];

        // $GLOBALS['link']->query("DELETE FROM `emojis` WHERE `name` = '{$name}'");
        $GLOBALS['link']->query("INSERT INTO `emojis`(`file`, `name`, `x`, `y`, `background_size`) VALUES ('{$file}', '{$name}', {$x}, {$y}, '{$background_size}')");
    }
?>