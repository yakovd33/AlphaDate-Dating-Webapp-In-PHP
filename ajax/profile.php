<?php
    require_once('../includes/config.php');
    require_once('../includes/functions.php');
    
    if (isset($_GET['type'])) {
        switch ($_GET['type']) {
            case 'update_col' :
                $editable_db_cols = [ 'fullname', 'nickname', 'email', 'gender', 'city', 'orientation', 'city', 'about_me', 'date_of_birth', 'interest_age_min', 'interest_age_max', 'profession', 'company', 'education', 'relationship', 'height', 'weight', 'body_type', 'hair_color', 'eye_color', 'children', 'smoking', 'alcohol', 'children' ];
                $relationship_values = [ 'רווק', 'גרוש', 'אלמן' ];
                $body_types_values = [ 'אתלטי', 'ממוצע', 'שרירי', 'רזה' ];
                $hair_color_values = [ 'חום בהיר','בלונד','חום כהה','שחור','צבוע','קירח','לבן','מגולח' ];
                $eye_color_values = [ 'שחור','חום','כחול','ירוק','אחר' ];
                $smoking_values = [ '0', '1' ];
                $alcohol_values = [ 'לא שותה', 'שותה מעט', 'שותה הרבה' ];

                if (isset($_POST['col'], $_POST['value'])) {
                    echo $col = $_POST['col'];
                    echo $value = $_POST['value'];

                    if (!empty($col) && (strlen($value) != 0)) {
                        if (in_array($col, $editable_db_cols)) {
                            $value = addslashes(ltrim(rtrim(strip_tags($value))));

                            if ($col == 'relationship' && !in_array($value, $relationship_values)) {
                                die();
                            }

                            if ($col == 'body_type' && !in_array($value, $body_types_values)) {
                                die();
                            }

                            if ($col == 'hair_color' && !in_array($value, $hair_color_values)) {
                                die();
                            }

                            if ($col == 'eye_color' && !in_array($value, $eye_color_values)) {
                                die();
                            }

                            if ($col == 'smoking' && !in_array($value, $smoking_values)) {
                                die();
                            }

                            if ($col == 'alcohol' && !in_array($value, $alcohol_values)) {
                                die();
                            }

                            if (($col == 'children') && ($value < 0 || $value > 10)) {
                                die();
                            }

                            if (($col == 'height') && ($value < 150 || $value > 220)) {
                                die();
                            }

                            if (($col == 'weight') && ($value < 45 || $value > 150)) {
                                die();
                            }

                            if (strlen($value) == 0) {
                                die();
                            }

                            $GLOBALS['link']->query("UPDATE `users` SET `{$col}` = '{$value}' WHERE `id` = {$_SESSION['user_id']}");
                            print_r($GLOBALS['link']->errorInfo());
                        }
                    } else {
                        echo 'empty';
                    }
                }

                break;

            case 'send_flower' :
                if (isset($_POST['userid'])) {
                    $userid = $_POST['userid'];

                    // TODO: Make this a mysql transaction

                    // Check if sender has the amount of flowers to send

                    if ($GLOBALS['link']->query("SELECT `flowers` FROM `users` WHERE `id` = {$_SESSION['user_id']}")->fetch()['flowers'] > 0) {
                        // Take flower from sender
                        $GLOBALS['link']->query("UPDATE `users` SET `flowers` = `flowers` - 1 WHERE `id` = {$_SESSION['user_id']}");

                        // Give flower to reciever
                        $GLOBALS['link']->query("UPDATE `users` SET `flowers` = `flowers` + 1 WHERE `id` = {$userid}");

                        // Insert flower to db
                        $GLOBALS['link']->query("INSERT INTO `sent_flowers`(`from_id`, `to_id`) VALUES ({$_SESSION['user_id']}, {$userid})");
                    }
                }

                break;
            case 'block-user' :
                if (isset($_POST['userid'])) {
                    $userid = $_POST['userid'];

                    // Check if not already blocked
                    if ($GLOBALS['link']->query("SELECT * FROM `blocked_users` WHERE `user_id` = {$_SESSION['user_id']} AND `blocked_id` = {$userid}")->rowCount() == 0) {
                        // Insert block
                        $GLOBALS['link']->query("INSERT INTO `blocked_users` (`user_id`, `blocked_id`) VALUES ({$_SESSION['user_id']}, {$userid})");
                    }
                }

                break;
        }
    }
?>