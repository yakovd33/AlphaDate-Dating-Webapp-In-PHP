<?php
    function get_hon () {
        $CUR_USER = get_user_row_by_id($_SESSION['user_id']);

        // Check if user has a hor item already
        $open_item_stmt = $GLOBALS['link']->query("SELECT * FROM `hot_or_not_voted` WHERE `voter_id` = {$_SESSION['user_id']} AND NOT `is_hearted` AND NOT `is_rejected`");
        if ($open_item_stmt->rowCount() == 0) {
            // Get potential users by interest
            $ages = [];
            $hair_colors = [];
            $eye_colors = [];
            $body_types = [];

            $hon_query = "SELECT *, YEAR(CURDATE()) - YEAR(`date_of_birth`) AS `age` FROM `users` WHERE 1";

            if ($CUR_USER['orientation'] != 'both') { 
                $hon_query .= ' AND `gender` = "' . $CUR_USER['orientation'] . '"';
            }

            // Activate when there are enough real users
            // $hon_query .= " AND (`orientation` = 'both' OR `orientation` = '" . $CUR_USER['gender'] . "')";

            $hon_query .= " AND (YEAR(`date_of_birth`) <= YEAR(CURDATE()) - " . $CUR_USER['interest_age_min'] . ")";
            $hon_query .= " AND (YEAR(`date_of_birth`) >= YEAR(CURDATE()) - " . $CUR_USER['interest_age_max'] . ")";
            $hon_query .= " AND `id` <> " . $_SESSION['user_id'];
            $hon_query .= " AND `is_in_hot_or_not`";
            $hon_query .= get_user_blocked_user_by_col('id');
            $hon_query .= get_banned_user_by_col('id');

            $hon_query .= " ORDER BY `popularity` DESC";

            $hon_stmt = $GLOBALS['link']->query($hon_query);

            $current_users = [];

            while ($user = $hon_stmt->fetch()) {
                $user['points'] = 0;
                array_push($current_users, $user);
            }

            // Filter users that been hearted/rejected in the last 30 days
            $new_current_users = [];
            for ($i = 0; $i < count($current_users); $i++) {
                if ($GLOBALS['link']->query("SELECT * FROM `hot_or_not_voted` WHERE `voter_id` = {$_SESSION['user_id']} AND `voted_id` = {$current_users[$i]['id']} AND `time_voted` > DATE_SUB(NOW(), INTERVAL 1 MONTH)")->rowCount() > 0) {
                } elseif (count(get_user_hon_pics($current_users[$i]['id'])) > 0) {
                    array_push($new_current_users, $current_users[$i]);
                }
            }

            $current_users = $new_current_users;
            
            if (count($current_users) > 1) {
                // Get previous hons averages
                $prev_stmt = $GLOBALS['link']->query("SELECT * FROM `hot_or_not_voted` WHERE `is_hearted` AND `voter_id` = {$CUR_USER['id']}");
                while ($user = $prev_stmt->fetch()) {
                    $user_dets = get_user_row_by_id($user['voted_id']);
                    array_push($ages, $user_dets['age']);

                    if ($user_dets['hair_color']) {
                        array_push($hair_colors, $user_dets['hair_color']);
                    }

                    if ($user_dets['eye_color']) {
                        array_push($eye_colors, $user_dets['eye_color']);
                    }

                    if ($user_dets['body_type']) {
                        array_push($body_types, $user_dets['body_type']);
                    }
                }

                if (count($ages) > 0) {
                    $avg_age = array_sum($ages) / count($ages);
                } else {
                    $avg_age = ($CUR_USER['interest_age_min'] + $CUR_USER['interest_age_max']) / 2;
                }

                if (count($hair_colors) > 0) {
                    $avg_hair_colors = array_search(max(array_count_values($hair_colors)), array_count_values($hair_colors));
                } else {
                    $avg_hair_colors = '';
                }

                if (count($eye_colors) > 0) {
                    $avg_eye_colors = array_search(max(array_count_values($eye_colors)), array_count_values($eye_colors));
                } else {
                    $avg_eye_colors = '';
                }

                if (count($body_types) > 0) {
                    $avg_body_types = array_search(max(array_count_values($body_types)), array_count_values($body_types));
                } else {
                    $avg_body_types = '';
                }

                for ($i = 0; $i < count($current_users) - 1; $i++) {
                    $user = $current_users[$i];

                    if ($user['hair_color'] == $avg_hair_colors) {
                        $user['points'] += 10;
                    }

                    if ($user['body_type'] == $avg_body_types) {
                        $user['points'] += 7;
                    }

                    if ($user['eye_color'] == $avg_eye_colors) {
                        $user['points'] += 5;
                    }

                    if ($user['age'] == $avg_age) {
                        $user['points'] += 5;
                    }

                    $current_users[$i] = $user;
                }
                
                usort($current_users, 'sortByPoints');
                array_reverse($current_users);
            }

            if (count($current_users) > 0) {
                $final_user = $current_users[0];

                // Required_info: name, age, city, popularity, num_images, images, profile pic
                $user_info = [];
                $user_info['userid'] = $final_user['id'];
                $user_info['fullname'] = $final_user['fullname'];
                $user_info['age'] = $final_user['age'];
                $user_info['city'] = $final_user['city'];
                $user_info['popularity'] = get_user_popularity($final_user['id']);
                $user_info['gender'] = ($final_user['gender'] == 'male' ? 'גבר' : 'אישה');
                $user_info['num_images'] = $GLOBALS['link']->query("SELECT * FROM `hot_or_not_pics` WHERE `user_id` = {$final_user['id']}")->rowCount();
                $user_info['images'] = get_user_hon_pics($final_user['id']);
                $user_info['pp'] = get_user_pp_by_id($final_user['id']);

                // Insert HON to db
                $GLOBALS['link']->query("INSERT INTO `hot_or_not_voted`(`voter_id`, `voted_id`) VALUES ({$_SESSION['user_id']}, {$final_user['id']})");

                return json_encode($user_info);
            } else {
                return 'not found';
            }
        } else {
            $final_user = get_user_row_by_id($open_item_stmt->fetch()['voted_id']);
            $pics_count =  $GLOBALS['link']->query("SELECT * FROM `hot_or_not_pics` WHERE `user_id` = {$final_user['id']}")->rowCount();
            
            if ($pics_count > 0) {
                $user_info = [];
                $user_info['userid'] = $final_user['id'];
                $user_info['fullname'] = $final_user['fullname'];
                $user_info['age'] = $final_user['age'];
                $user_info['city'] = $final_user['city'];
                $user_info['popularity'] = get_user_popularity($final_user['id']);
                $user_info['gender'] = ($final_user['gender'] == 'male' ? 'גבר' : 'אישה');
                $user_info['num_images'] = $pics_count;
                $user_info['images'] = get_user_hon_pics($final_user['id']);
                $user_info['pp'] = get_user_pp_by_id($final_user['id']);
            } else {
                // Remove open card
                $GLOBALS['link']->query("DELETE FROM `hot_or_not_voted` WHERE `voter_id` = {$_SESSION['user_id']} AND NOT `is_hearted` AND NOT `is_rejected`");
                
                // Return new card
                get_hon();
            }

            return json_encode($user_info);
        }
    }

    function get_user_hon_pics ($user_id) {
        $user_pics_stmt = $GLOBALS['link']->query("SELECT * FROM `hot_or_not_pics` WHERE `user_id` = {$user_id}");
        $images = [];

        while ($image = $user_pics_stmt->fetch()) {
            array_push($images, get_image_path_by_id($image['image_id']));
        }

        return $images;
    }

    function get_current_hon () {
        $open_item_stmt = $GLOBALS['link']->query("SELECT * FROM `hot_or_not_voted` WHERE `voter_id` = {$_SESSION['user_id']} AND NOT `is_hearted` AND NOT `is_rejected`");
        if ($open_item_stmt->rowCount() != 0) {
            return $open_item_stmt->fetch();
        } else {
            return false;
        }
    }

    function heart () {
        $cur_hon = get_current_hon();

        if ($cur_hon) {
            // Update is_hearted
            $GLOBALS['link']->query("UPDATE `hot_or_not_voted` SET `is_hearted` = 1 WHERE `id` = {$cur_hon['id']}");

            // Check if other side hearted too
            if ($GLOBALS['link']->query("SELECT * FROM `hot_or_not_voted` WHERE `voted_id` = {$cur_hon['voter_id']} AND `voter_id` = {$cur_hon['voted_id']}")->rowCount() > 0) {
                // Create a match
                $GLOBALS['link']->query("INSERT INTO `hot_or_not_matches`(`user_one_id`, `user_two_id`) VALUES ({$cur_hon['voter_id']}, {$cur_hon['voted_id']})");
            }

            // Give hearted user popularity
            increase_user_popularity($cur_hon['voted_id'], $CUR_USER['popularity'] / 10 + 2);
        }
    }

    function reject () {
        $cur_hon = get_current_hon();
        
        if ($cur_hon) {
            // Update is_hearted
            $GLOBALS['link']->query("UPDATE `hot_or_not_voted` SET `is_rejected` = 1 WHERE `id` = {$cur_hon['id']}");

            // Decrease rejected user popularity
            increase_user_popularity($cur_hon['voted_id'], -1);
        }
    }
?>