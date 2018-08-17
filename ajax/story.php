<?php
    require_once('../includes/config.php');
    require_once('../includes/functions.php');
    
    if (isset($_GET['type'])) {
        switch ($_GET['type']) {
            case 'upload' :
                if (isset($_FILES['img'], $_POST['text'], $_POST['color'], $_POST['isBg'])) {
                    $img = $_FILES['img'];
                    $text = addslashes(htmlentities($_POST['text']));
                    $color = addslashes(htmlentities($_POST['color']));
                    $isBg = $_POST['isBg'];

                    if (check_csrf()) {
                        die('CSRF DETECTED!');
                    }

                    $img_id = insert_photo($img, 'story-pics', 'story');

                    $GLOBALS['link']->query("INSERT INTO `stories`(`user_id`, `image_id`, `text`, `text_color`, `is_text_with_bg`) VALUES ({$_SESSION['user_id']}, {$img_id}, '{$text}', '{$color}', {$isBg})");
                    print_r($GLOBALS['link']->errorInfo());
                }

                break;
            case 'get' :
                if ($_GET['storyid']) {
                    $story_id = $_GET['storyid'];

                    // Check if story exists
                    $story_stmt = $GLOBALS['link']->query("SELECT * FROM `stories` WHERE `id` = {$story_id} AND `date` > DATE_SUB(NOW(), INTERVAL 1 DAY)");
                    if ($story_stmt->rowCount() > 0) {
                        $story = $story_stmt->fetch();
                        $story_poster = get_user_row_by_id($story['user_id']);

                        // Check if user is not blocked from seeing this story
                        if ($GLOBALS['link']->query("SELECT * FROM `blocked_users` WHERE `user_id` = {$story_poster['id']} AND `blocked_id` = {$_SESSION['user_id']}")->rowCount() == 0 && !is_user_blocked($story_poster['id'])) {
                            $resp = [];
                            $resp['time'] = friendly_time($story['date']);
                            $resp['img'] = base64_encode(file_get_contents('../' . get_image_path_by_id($story['image_id'])));
                            $resp['text'] = $story['text'];
                            $resp['color'] = $story['text_color'];
                            $resp['isBg'] = $story['is_text_with_bg'];

                            // Set story view
                            if ($story['user_id'] != $_SESSION['user_id']) {
                                // Check if not already viewed
                                if ($GLOBALS['link']->query("SELECT * FROM `story_views` WHERE `user_id` = {$_SESSION['user_id']} AND `story_id` = {$story_id}")->rowCount() == 0) {
                                    $GLOBALS['link']->query("INSERT INTO `story_views`(`user_id`, `story_id`) VALUES ({$_SESSION['user_id']}, {$story_id})");
                                }
                            }

                            echo json_encode($resp);
                        }
                    }
                }

                break;
            case 'get_user_stories' :
                if (isset($_GET['userid'])) {
                    $uid = $_GET['userid'];
                    $user = get_user_row_by_id($uid);

                    $resp = [];
                    $resp['fullname'] = $user['fullname'];
                    $resp['pp'] = get_user_pp_by_id($uid);
                    $resp['stories'] = [];

                    $user_stories_stmt = $GLOBALS['link']->query("SELECT * FROM `stories` WHERE `user_id` = {$uid} AND `date` > DATE_SUB(NOW(), INTERVAL 1 DAY) ORDER BY `date`");

                    while ($story = $user_stories_stmt->fetch()) {
                        // array_push($stories, $story);
                        $story_item = [];
                        $story_item['id'] = $story['id'];

                        array_push($resp['stories'], $story_item);
                    }

                    echo json_encode($resp);
                }

                break;
        }
    }
?>