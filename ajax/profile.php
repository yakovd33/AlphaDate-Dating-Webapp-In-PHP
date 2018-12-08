<?php
    require_once('../includes/config.php');
    require_once('../includes/functions.php');
    
    if (isset($_GET['type'])) {
        switch ($_GET['type']) {
            case 'update_col' :
                $editable_db_cols = [ 'fullname', 'nickname', 'email', 'gender', 'city', 'orientation', 'city', 'about_me', 'date_of_birth', 'interest_age_min', 'interest_age_max', 'profession', 'company', 'education', 'relationship', 'height', 'weight', 'body_type', 'hair_color', 'eye_color', 'children', 'smoking', 'alcohol', 'children', 'zodiac' ];
                $relationship_values = [ 'רווק', 'גרוש', 'אלמן' ];
                $body_types_values = [ 'אתלטי', 'ממוצע', 'שרירי', 'רזה' ];
                $hair_color_values = [ 'חום בהיר','בלונד','חום כהה','שחור','צבוע','קירח','לבן','מגולח' ];
                $eye_color_values = [ 'שחור','חום','כחול','ירוק','אחר' ];
                $smoking_values = [ '0', '1' ];
                $alcohol_values = [ 'לא שותה', 'שותה מעט', 'שותה הרבה' ];
                $zodiac_values = [ 'טלה','שור','תאומים','סרטן','אריה','בתולה','מאזניים','עקרב','קשת','גדי','דלי','דגים' ];
                $orientation_values = [ 'male', 'female', 'both' ];

                if (isset($_POST['col'], $_POST['value'])) {
                    $col = $_POST['col'];
                    $value = $_POST['value'];

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

                            if ($col == 'zodiac' && !in_array($value, $zodiac_values)) {
                                die();
                            }

                            if ($col == 'orientation' && !in_array($value, $orientation_values)) {
                                die();
                            }

                            if (($col == 'interest_age_min' || $col == 'interest_age_max') && ($value < 0 || $value > 120)) {
                                die();
                            }

                            if (strlen($value) == 0) {
                                die();
                            }

                            $GLOBALS['link']->query("UPDATE `users` SET `{$col}` = '{$value}' WHERE `id` = {$_SESSION['user_id']}");
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
                    
                        // Give popularity to sent user if is first flower sent today
                        if ($GLOBALS['link']->query("SELECT * FROM `sent_flowers` WHERE `from_id` = {$_SESSION['user_id']} AND `to_id` = {$userid} AND DATE(`date`) = DATE(NOW())")->rowCount() == 0) {
                            increase_user_popularity($userid, 4);
                        }

                        d_log("SELECT * FROM `sent_flowers` WHERE `from_id` = {$_SESSION['user_id']} AND `to_id` = {$userid} AND DATE(`date`) = DATE(NOW())");
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

            case 'set-pp' :
                if (isset($_FILES['pic'])) {
                    $file = $_FILES['pic'];
                    echo $photo_id = insert_photo($file, 'profile-pics', 'pp');

                    $GLOBALS['link']->query("UPDATE `users` SET `pp_id` = {$photo_id} WHERE `id` = {$_SESSION['user_id']}");
                }

                break;
            case 'delete_hobby' :
                if (isset($_POST['hobby_id'])) {
                    // Check if user is the owner of the hobby
                    $hobby_id = $_POST['hobby_id'];
                    if ($GLOBALS['link']->query("SELECT * FROM `users_hobbies` WHERE `id` = {$hobby_id} AND `user_id` = {$_SESSION['user_id']}")->rowCount() > 0) {
                        $GLOBALS['link']->query("UPDATE `users_hobbies` SET `active` = 0 WHERE `id` = {$hobby_id}");
                    }
                }

                break;

            case 'new_hobby' :
                if (isset($_POST['text'])) {
                    $text = trim(addslashes(htmlentities($_POST['text'])));

                    $GLOBALS['link']->query("INSERT INTO `users_hobbies`(`user_id`, `text`) VALUES ({$_SESSION['user_id']}, '{$text}')");
                    echo $GLOBALS['link']->lastInsertId();
                }

                break;
            case 'change_password' :
                if (isset($_POST['password'], $_POST['token'])) {
                    $token = $_POST['token'];
                    $password = $_POST['password'];
                    $user_id = $GLOBALS['link']->query("SELECT * FROM `password_reset_tokens` WHERE `token` = '{$token}'")->fetch()['user_id'];
                    $user = get_user_row_by_id($user_id);

                    if (strlen($password) > 0) {
                        change_user_password($user_id, $password);

                        // Update login token and setting it as user
                        $GLOBALS['link']->query("UPDATE `password_reset_tokens` SET `is_used` = 1 WHERE `token` = '{$token}'");
                        echo 'success';
                    }
                }
        }
    }
?>