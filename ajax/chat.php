<?php
    require_once('../includes/config.php');
    require_once('../includes/functions.php');
    
    if (isset($_GET['type'])) {
        switch ($_GET['type']) {
            case 'get_checkbox' :
                if (isset($_GET['id'])) {

                    if ($_GET['id'] == $_SESSION['user_id']) {
                        die();
                    }

                    $resp = [];

                    $id = $_GET['id'];
                    $user = get_user_row_by_id($id);

                    $resp['userid'] = $id;
                    $resp['fullname'] = $user['fullname'];

                    $chat_messages = [];
                    $chat_messages_query = $GLOBALS['link']->query("SELECT * FROM `messages` WHERE (`from_id` = {$_SESSION['user_id']} AND `to_id` = {$id}) OR (`from_id` = {$id} AND `to_id` = {$_SESSION['user_id']}) ORDER BY `id`");

                    while ($message = $chat_messages_query->fetch()) {
                        $message_image = false;

                        if ($message['image_id']) {
                            $message_image = get_image_path_by_id($message['image_id']);
                        }

                        array_push($chat_messages, [
                            'userid' => $message['from_id'],
                            'text' => $message['message'],
                            'date' => $message['date'],
                            'isSelf' => ($message['from_id'] == $_SESSION['user_id']),
                            'image' => $message_image,
                        ]);
                    }

                    $resp['messages'] = $chat_messages;
                    $resp['isLogged'] = is_user_logged($id);

                    if ($GLOBALS['link']->query("SELECT * FROM `open_chatboxes` WHERE `user_id` = {$_SESSION['user_id']} AND `to_id` = {$id}")->rowCount() == 0) {
                        // Add chatbox to users open chatboxes
                        $num_open_chatboxes = $GLOBALS['link']->query("SELECT * FROM `open_chatboxes` WHERE `user_id` = {$_SESSION['user_id']}")->rowCount();
                        if ($num_open_chatboxes >= 3) {
                            // Delete first open chat
                            $GLOBALS['link']->query("DELETE FROM `open_chatboxes` WHERE `user_id` = {$_SESSION['user_id']} ORDER BY `id` LIMIT 1");
                        }

                        // Insert chatbox
                        $GLOBALS['link']->query("INSERT INTO `open_chatboxes`(`user_id`, `to_id`) VALUES ({$_SESSION['user_id']}, {$id})");
                    }

                    echo json_encode($resp);
                }

                break;
            case 'send_message' :
                $resp = [];
                if (isset($_POST['userid'], $_POST['text'])) {
                    $userid = $_POST['userid'];
                    $text = $_POST['text'];

                    // Prevents empty messages and messages sent to current user
                    if ((!empty($text) && $userid != $_SESSION['user_id']) || (empty($text) && isset($_FILES['pic']))) {                        
                        $image_id = null;
                        // In case of pic
                        if (isset($_FILES['pic'])) {
                            $pic = $_FILES['pic'];
                            $image_id = insert_photo($pic, 'chat-pics', 'message');
                            $resp['image'] = get_image_path_by_id($image_id);
                        }

                        $insert_msg_prep_msg = $GLOBALS['link']->prepare("INSERT INTO `messages`(`from_id`, `to_id`, `message`, `image_id`) VALUES (?, ?, ?, ?)");
                        $insert_msg_prep_msg->execute([ $_SESSION['user_id'], $userid, $text, $image_id]);
                        $message_id = $GLOBALS['link']->lastInsertId();
                        $message = $GLOBALS['link']->query("SELECT * FROM `messages` WHERE `id` = {$message_id}")->fetch();
                    }

                    
                    if ($GLOBALS['link']->query("SELECT * FROM `open_chatboxes` WHERE `user_id` = {$userid} AND `to_id` = {$_SESSION['user_id']}")->rowCount() == 0) {
                        $num_open_chatboxes = $GLOBALS['link']->query("SELECT * FROM `open_chatboxes` WHERE `user_id` = {$userid}")->rowCount();
                        if ($num_open_chatboxes >= 3) {
                            // Delete first open chat
                            $GLOBALS['link']->query("DELETE FROM `open_chatboxes` WHERE `user_id` = {$_SESSION['user_id']} ORDER BY `id` LIMIT 1");
                        }

                        // Open chatbox to other user
                        $GLOBALS['link']->query("INSERT INTO `open_chatboxes`(`user_id`, `to_id`) VALUES ({$userid}, {$_SESSION['user_id']})");
                    }

                    // Add message to pending messages
                    $GLOBALS['link']->query("INSERT INTO `pending_messages`(`to_id`, `message_id`) VALUES ({$userid}, {$message_id})");
                
                    // Check if message is first between the users
                    if ($GLOBALS['link']->query("SELECT * FROM `messages` WHERE (`from_id` = {$_SESSION['user_id']} AND `to_id` = {$userid}) OR (`from_id` = {$userid} AND `to_id` = {$_SESSION['user_id']})")->rowCount() == 1) {
                        $sender_popularity = get_user_popularity($message['from_id']);

                        $popularity_increase = 0;
                        if ($sender_popularity >= 0 && $sender_popularity < 60) {
                            // 0-50: Give 2
                            $popularity_increase = 2;
                        } elseif ($sender_popularity >= 60 && $sender_popularity < 70) {
                            // 60+ Give 3
                            $popularity_increase = 3;
                        } elseif ($sender_popularity >= 70 && $sender_popularity < 80) {
                            // 70+ Give 4
                            $popularity_increase = 4;
                        } elseif ($sender_popularity >= 80 && $sender_popularity < 90) {
                            // 80+ Give 5
                            $popularity_increase = 5;
                        } elseif ($sender_popularity >= 90) {
                            // 90+ Give 8
                            $popularity_increase = 6;
                        }

                        increase_user_popularity($userid, $popularity_increase);
                    }
                }
                
                echo json_encode($resp);
                break;
            case 'close_chatbox' :
                if (isset($_GET['id'])) {
                    $id = $_GET['id'];
                    $GLOBALS['link']->query("DELETE FROM `open_chatboxes` WHERE `user_id` = {$_SESSION['user_id']} AND `to_id` = {$id}");
                }

                break;
            case 'fold_chatbox' :
                if (isset($_GET['id'])) {
                    $id = $_GET['id'];
                    $GLOBALS['link']->query("UPDATE `open_chatboxes` SET `is_folded` = NOT `is_folded` WHERE `user_id` = {$_SESSION['user_id']} AND `to_id` = {$id}");
                }

                break;
            case 'messages_listen' :
                $pending_messages_stmt = $GLOBALS['link']->query("SELECT * FROM `pending_messages` WHERE `to_id` = {$_SESSION['user_id']}");
                $messages = [];

                while ($pending_message = $pending_messages_stmt->fetch()) {
                    $message = $GLOBALS['link']->query("SELECT * FROM `messages` WHERE `id` = {$pending_message['message_id']} ORDER BY `id`")->fetch();
                    $message_image = false;

                    if ($message['image_id']) {
                        $message_image = get_image_path_by_id($message['image_id']);
                    }

                    array_push($messages, [
                        'userid' => $message['from_id'],
                        'text' => $message['message'],
                        'date' => $message['date'],
                        'isSelf' => false,
                        'image' => $message_image,
                    ]);
                }

                // Delete pending messages
                $GLOBALS['link']->query("DELETE FROM `pending_messages` WHERE `to_id` = {$_SESSION['user_id']}");

                echo json_encode($messages);

                break;
        }
    }
?>