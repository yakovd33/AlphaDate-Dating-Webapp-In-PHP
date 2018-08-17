<?php
    function passsword_hash ($password) {
        return password_hash($password, PASSWORD_BCRYPT, array("cost" => 10));
    }

    function is_logged () {
        return (isset($_SESSION['user_id']) && $_SESSION['user_id'] != 0);
    }

    function email_exists ($email) {
        return ($GLOBALS['link']->query("SELECT `email` FROM `users` WHERE `email` = '{$email}'")->rowCount() > 0);       
    }

    function do_email_and_pass_match ($email, $pass) {
        $db_pass = $GLOBALS['link']->query("SELECT `password_hashed` FROM `users` WHERE `email` = '{$email}'")->fetch()['password_hashed'];        
        return password_verify($pass, $db_pass);
    }

    function get_user_id_by_email ($email) {
        return ($GLOBALS['link']->query("SELECT `id` FROM `users` WHERE `email` = '{$email}'"))->fetch()['id'];
    }

    function logout () {
        setcookie('login_hash', "", time() -10000, '/');
        $_SESSION['user_id'] = 0;
    }

    function get_user_row_by_id ($id) {
        $user = $GLOBALS['link']->query("SELECT *, YEAR(CURDATE()) - YEAR(`date_of_birth`) AS `age` FROM `users` WHERE `id` = {$id}")->fetch();
        $user['meetings'] = $GLOBALS['link']->query("SELECT * FROM `meetings_requests` WHERE (`user_one_id` = {$id} OR `user_two_id` = {$id}) AND `is_approved`")->rowCount();
        return $user;
    }

    function update_last_seen () {
        $GLOBALS['link']->query("UPDATE `users` SET `last_connected` = NOW() WHERE `id` = {$_SESSION['user_id']}");
        register_login_day();
    }

    function register_login_day () {
        // Check if today is already registered
        if ($GLOBALS['link']->query("SELECT * FROM `users_connected_days` WHERE `user_id` = {$_SESSION['user_id']} AND `date` = CURDATE()")->rowCount() == 0) {
            // Insert today
            $GLOBALS['link']->query("INSERT INTO `users_connected_days`(`user_id`, `date`) VALUES ({$_SESSION['user_id']}, CURDATE())");
        }
    }

    function genderize_text ($male_form) {
        $CUR_USER = get_user_row_by_id($_SESSION['user_id']);
        
        if (is_logged()) {
            if ($CUR_USER['gender'] == 'female') {
                switch ($male_form) {
                    case 'כתוב' :
                        return 'כתבי';
                        break;
                    case 'שלח' :
                        return 'שלחי';
                        break;
                    case 'חסום' :
                        return 'חסמי';
                        break;

                    case 'רווק' :
                        return 'רווקה';
                        break;

                    case 'גרוש' :
                        return 'גרושה';
                        break;

                    case 'אלמן' :
                        return 'אלמנה';
                        break;
                }
            } else {
                return $male_form;
            }
        } else {
            return $male_form;
        }
    }

    function get_user_popularity ($userid) {
        $top_popularity = $GLOBALS['link']->query("SELECT `popularity` FROM `users` ORDER BY `popularity` DESC LIMIT 1")->fetch()['popularity'];
        $current_popularity = $GLOBALS['link']->query("SELECT `popularity` FROM `users` WHERE `id` = {$userid}")->fetch()['popularity'];
        if ($current_popularity != 0) {
            return floor(($current_popularity / $top_popularity) * 100);
        } else {
            return 0;
        }
    }
   
    function increase_user_popularity ($userid, $amount) {
        $GLOBALS['link']->query("UPDATE `users` SET `popularity` = `popularity` + {$amount} WHERE `id` = {$userid}");
    }

    function decrease_user_popularity ($userid, $amount) {
        $GLOBALS['link']->query("UPDATE `users` SET `popularity` = `popularity` + {$amount} WHERE `id` = {$userid}");
    }

    function insert_photo ($file, $uploads_dir, $type = false) {
        // Move to folder
        $tmp_name = $file["tmp_name"];
        $name = md5(date('Y-m-d H:i:s:u') . time() . rand(0, 10000));
        $file_name = $file['name'];
        $ext = pathinfo($file['name'], PATHINFO_EXTENSION);

        if ($file['size'] != 0 && $file['error'] == 0 && $file['size'] < 2000000) {
            $allowed_extensions = array('image/png', 'image/jpg', 'image/jpeg');
            if (in_array($file['type'], $allowed_extensions)) {
                $final_path = "/" . $name . "." . $ext;
                $destination = realpath(__DIR__ . '../../uploads/' . $uploads_dir) . '/' . $name . '.' . $ext;
                move_uploaded_file($tmp_name, $destination);

                // Insert to DB
                $path =  $uploads_dir . '/' . $name . '.' . $ext;
                if ($type == false) {
                    $GLOBALS['link']->query("INSERT INTO `images`(`user_id`, `path`) VALUES ({$_SESSION['user_id']}, '{$path}')");
                } else {
                    $GLOBALS['link']->query("INSERT INTO `images`(`user_id`, `path`, `is_{$type}`) VALUES ({$_SESSION['user_id']}, '{$path}', true)");
                }

                return $GLOBALS['link']->lastInsertId();
            }
        }
    }

    function is_cur_user_profile_complete () {
        $CUR_USER = get_user_row_by_id($_SESSION['user_id']);
        return ($CUR_USER['orientation'] != null && $CUR_USER['city'] != null);
    }

    function get_user_num_hon_pics ($user_id) {
        return $GLOBALS['link']->query("SELECT * FROM `hot_or_not_pics` WHERE `user_id` = {$user_id}")->rowCount();
    }

    function sortByPoints($a, $b) {
        if ($a['points'] != $b['points']) {
            return $a['points'] < $b['points'];
        } elseif ($a['popularity'] != $b['popularity']) {
            return $a['popularity'] < $b['popularity'];
        } else {
            return $a['is_paid_user'];
        }
    }

    function sort_by_date ($a, $b) {
        return $a['date'] < $b['date'];
    }
    
    function get_login_hash () {
        $hash = md5(time() . rand(0, 99999) . rand(0, 9999));
        $GLOBALS['link']->query("INSERT INTO `login_hashes`(`user_id`, `hash`) VALUES ({$_SESSION['user_id']}, '{$hash}')");
        return $hash;
    }

    function ranged_options ($min, $max, $default = false) {
        for ($i = $min; $i <= $max; $i++) {
            if ($default != false) {
                $checked = ($i == $default) ? 'selected="selected"' : '';
                echo "<option value=\"$i\" $checked>$i</option>";
            } else {
                echo "<option value=\"$i\">$i</option>";
            }
        }
    }

    function height_format ($cm) {
        return $cm;
    }

    function get_user_unseen_matches_num () {
        $num = 0;
        $num += $GLOBALS['link']->query("SELECT * FROM `hot_or_not_matches` WHERE `user_one_id` = {$_SESSION['user_id']} AND NOT `user_one_seen`")->rowCount();
        $num += $GLOBALS['link']->query("SELECT * FROM `hot_or_not_matches` WHERE `user_two_id` = {$_SESSION['user_id']} AND NOT `user_two_seen`")->rowCount();
        return $num < 10 ? $num : '9+';
    }

    function get_user_unseen_meetings_requests_num () {
        $num = 0;
        $num += $GLOBALS['link']->query("SELECT * FROM `meetings_requests` WHERE `user_one_id` = {$_SESSION['user_id']} AND NOT `user_one_seen`")->rowCount();
        $num += $GLOBALS['link']->query("SELECT * FROM `meetings_requests` WHERE `user_two_id` = {$_SESSION['user_id']} AND NOT `user_two_seen`")->rowCount();
        return $num < 10 ? $num : '9+';
    }

    function get_setting ($name) {
        return $GLOBALS['link']->query("SELECT `value` FROM `settings` WHERE `name` = '{$name}'")->fetch()['value'];
    }

    function set_setting ($name, $value) {
        return $GLOBALS['link']->query("UPDATE `settings` SET `value` = '{$value}' WHERE `name` = '{$name}'")->fetch()['value'];
    }

    function get_image_path_by_id ($id) {
        return 'uploads/' . $GLOBALS['link']->query("SELECT `path` FROM `images` WHERE `id` = {$id}")->fetch()['path'];
    }

    function d_log ($msg) {
        $GLOBALS['link']->query("INSERT INTO `log`(`message`) VALUES ('{$msg}')");
    }

    function is_user_logged ($user_id) {
        return ($GLOBALS['link']->query("SELECT * FROM `users` WHERE `id` = {$user_id} AND `last_connected` > DATE_SUB(NOW(), INTERVAL 10 MINUTE)")->rowCount() > 0);
    }

    function get_user_pp_by_id ($user_id) {
        global $URL;

        $user = get_user_row_by_id($user_id);
        if ($user['pp_id']) {
            $user['pp_path'] = get_image_path_by_id($user['pp_id']);

            if (!$user['pp_path']) {
                return $URL . '/img/pp.jpg';
            } else {
                return $URL . '/' . $user['pp_path'];
            }
        } else {
            return $URL . '/img/pp.jpg';
        }
    }

    function get_user_blocked_user_by_col ($col) {
        $blocked_stmt =  $GLOBALS['link']->query("SELECT * FROM `blocked_users` WHERE `user_id` = {$_SESSION['user_id']}");
        $blocked_str = "";

        while ($user = $blocked_stmt->fetch()) {
            $blocked_str .= " AND `$col` <> " . $user['blocked_id'];
        }

        return $blocked_str;
    }

    function is_user_blocked ($user_id) {
        return $GLOBALS['link']->query("SELECT * FROM `blocked_users` WHERE `user_id` = {$_SESSION['user_id']} AND `blocked_id` = {$user_id}")->rowCount() > 0;
    }

    function has_user_seen_story ($story_id) {
        return ($GLOBALS['link']->query("SELECT * FROM `story_views` WHERE `user_id` = {$_SESSION['user_id']} AND `story_id` = {$story_id}")->rowCount() > 0);
    }

    function get_user_chatgroup_list_by_col ($col) {
        $user_group_chats_stmt = $GLOBALS['link']->query("SELECT * FROM `chat_groups_members` WHERE `user_id` = {$_SESSION['user_id']}");
        $ret = " AND (";

        while ($chat_group = $user_group_chats_stmt->fetch()) {
            $ret .= " `$col` = " . $chat_group['group_id'] . " OR ";
        }

        $ret .= " 0)";

        return $ret;
    }

    function friendly_time ($timestamp) {
        $current_timestamp = $GLOBALS['link']->query("SELECT NOW() AS `timestamp`")->fetch()['timestamp'];
        $time_delta = strtotime($current_timestamp) - strtotime($timestamp);
        
        if($time_delta <= 1) return 'לפני פחות משנייה';
        if($time_delta < (60)) return 'לפני ' . $time_delta . ' שניות';
        if($time_delta < (60*60)) { $minutes = round($time_delta/60); return 'לפני כ' . $minutes . ' דקות'; }
        if($time_delta < (60*60*16)) { $hours = round($time_delta/(60*60)); return 'לפני כ' . $hours . ($hours > 1 ? ' שעות' : ''); }
        if($time_delta < (60*60*24)) { $hours = round($time_delta/(60*60)); return 'לפני כ' . $hours . ' שעות'; }
        if($time_delta < (time() - strtotime('yesterday'))) return 'אתמול';
        if($time_delta < (60*60*24*6.5)) return 'לפני כ' . round($time_delta/(60*60*24)) . ' ימים';
        if($time_delta < (time() - strtotime('last week'))) return 'שבוע שעבר';
        if(round($time_delta/(60*60*24*7))  == 1) return 'לפני כשבוע';
        if($time_delta < (60*60*24*7*3.5)) return 'לפני כ' . round($time_delta/(60*60*24*7)) . ' שבועות';
        if($time_delta < (time() - strtotime('last month'))) return 'חודש שעבר';
        if(round($time_delta/(60*60*24*7*4))  == 1) return 'לפני כחודש';
        if($time_delta < (60*60*24*7*4*11.5)) return 'about ' . round($time_delta/(60*60*24*7*4)) . ' חודשים';
        if($time_delta < (time() - strtotime('last year'))) return 'שנה שעבר';
        if(round($time_delta/(60*60*24*7*52)) == 1) return 'לפני כשנה';
        if($time_delta >= (60*60*24*7*4*12)) return 'לפני כ' . round($time_delta/(60*60*24*7*52)) . ' שנים'; 
        return false;
    }

    function random_color_part() {
        return str_pad( dechex( mt_rand( 0, 255 ) ), 2, '0', STR_PAD_LEFT);
    }
    
    function random_color() {
        return random_color_part() . random_color_part() . random_color_part();
    }

    function get_num_unread_messages () {
        return $GLOBALS['link']->query("SELECT * FROM `messages` WHERE `to_id` = {$_SESSION['user_id']} AND NOT `seen`")->rowCount() + $GLOBALS['link']->query("SELECT * FROM `unseen_group_messages` WHERE `user_id` = {$_SESSION['user_id']}")->rowCount();
    }

    function check_csrf () {
        return (!isset($_POST['csrf_token']) || $_POST['csrf_token'] != $_SESSION['csrf_token']);
    }

    function is_last_message_with_user_self ($userid) {
        return ($GLOBALS['link']->query("SELECT * FROM `messages` WHERE (`from_id` = {$_SESSION['user_id']} OR `from_id` = {$userid}) AND (`to_id` = {$_SESSION['user_id']} OR `to_id` = {$userid}) ORDER BY `date` DESC LIMIT 1")->fetch()['from_id'] == $_SESSION['user_id']);   
    }

    function has_user_read_last_message ($userid) {
        return ($GLOBALS['link']->query("SELECT * FROM `messages` WHERE ((`from_id` = {$_SESSION['user_id']} OR `from_id` = {$userid}) AND (`to_id` = {$_SESSION['user_id']} OR `to_id` = {$userid})) ORDER BY `date` DESC LIMIT 1")->fetch()['seen']);
    }
?>