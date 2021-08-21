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
                    $resp['pp'] = get_user_pp_by_id($id);
                    $resp['fullname'] = $user['fullname'];
                    $resp['profile_hash'] = $user['profile_hash'];

                    $chat_messages = [];
                    $chat_messages_query = $GLOBALS['link']->query("SELECT * FROM `messages` WHERE (`from_id` = {$_SESSION['user_id']} AND `to_id` = {$id}) OR (`from_id` = {$id} AND `to_id` = {$_SESSION['user_id']}) ORDER BY `id`");
             
                    while ($message = $chat_messages_query->fetch()) {
                        $message_image = false;

                        if ($message['image_id']) {
                            $message_image = get_image_path_by_id($message['image_id']);
                        }

                        array_push($chat_messages, [
                            'userid' => $message['from_id'],
                            'text' => emojify_message($message['message']),
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
            case 'get_group_chatbox' :
                if (isset($_GET['id'])) {
                    $resp = [];

                    $id = $_GET['id'];
                    $group = $GLOBALS['link']->query("SELECT * FROM `chat_groups` WHERE `id` = {$id}")->fetch();

                    $resp['groupid'] = $id;
                    $resp['name'] = $group['name'];

                    $chat_messages = [];
                    $chat_messages_query = $GLOBALS['link']->query("SELECT * FROM `messages` WHERE `group_id` = {$id} ORDER BY `id`");

                    while ($message = $chat_messages_query->fetch()) {
                        $message_image = false;
                        $message_user = get_user_row_by_id($message['from_id']);

                        if ($message['image_id']) {
                            $message_image = get_image_path_by_id($message['image_id']);
                        }

                        array_push($chat_messages, [
                            'userid' => $message['from_id'],
                            'fullname' => $message_user['fullname'],
                            'text' => emojify_message($message['message']),
                            'date' => $message['date'],
                            'isSelf' => ($message['from_id'] == $_SESSION['user_id']),
                            'image' => $message_image,
                        ]);
                    }

                    $resp['messages'] = $chat_messages;
                    $resp['isLogged'] = is_user_logged($id);

                    if ($GLOBALS['link']->query("SELECT * FROM `open_chatboxes` WHERE `user_id` = {$_SESSION['user_id']} AND `group_id` = {$id}")->rowCount() == 0) {
                        // Add chatbox to users open chatboxes
                        $num_open_chatboxes = $GLOBALS['link']->query("SELECT * FROM `open_chatboxes` WHERE `user_id` = {$_SESSION['user_id']}")->rowCount();
                        if ($num_open_chatboxes >= 3) {
                            // Delete first open chat
                            $GLOBALS['link']->query("DELETE FROM `open_chatboxes` WHERE `user_id` = {$_SESSION['user_id']} ORDER BY `id` LIMIT 1");
                        }

                        // Insert chatbox
                        $GLOBALS['link']->query("INSERT INTO `open_chatboxes`(`user_id`, `group_id`) VALUES ({$_SESSION['user_id']}, {$id})");
                    }

                    echo json_encode($resp);
                }

                break;
            case 'send_message' :
                $resp = [];
                if (isset($_POST['text']) && (isset($_POST['userid']) || isset($_POST['groupid']))) {
                    $is_user = false;
                    $is_group = false;

                    if (isset($_POST['userid'])) {
                        $userid = $_POST['userid'];
                        $is_user = true;
                    }

                    if (isset($_POST['groupid'])) {
                        $groupid = $_POST['groupid'];
                        $is_group = true;
                    }

                    $text = $_POST['text']; 
                    $text = str_replace("&lt;br /&gt;","\n",$text);
                    $text = str_replace("&lt;br&gt;",'\n',$text);
                    $text = htmlentities($text);
                    $text = preg_replace("/\s|&amp;nbsp;/",' ',$text);
                    $text = preg_replace("/\s|&nbsp;/",' ',$text);
                    $text = preg_replace("/\s|&amp;/",' ',$text);

                    // Prevents empty messages and messages sent to current user
                    if (($is_user && $userid == $_SESSION['user_id'])) {
                        die();
                    }

                    if ((!empty(trim($text)) || (empty($text) && isset($_FILES['pic'])))) {                        
                        $image_id = null;
                        // In case of pic
                        if (isset($_FILES['pic'])) {
                            $pic = $_FILES['pic'];
                            $image_id = insert_photo($pic, 'chat-pics', 'message');
                            $resp['image'] = get_image_path_by_id($image_id);
                        }

                        if ($is_user) {
                            $insert_msg_prep_msg = $GLOBALS['link']->prepare("INSERT INTO `messages`(`from_id`, `to_id`, `message`, `image_id`) VALUES (?, ?, ?, ?)");
                            $insert_msg_prep_msg->execute([ $_SESSION['user_id'], $userid, $text, $image_id]);
                        } elseif ($is_group) {

                            // Check if user is in group
                            if ($GLOBALS['link']->query("SELECT * FROM `chat_groups_members` WHERE `user_id` = {$_SESSION['user_id']} AND `group_id` = {$groupid}")->rowCount() > 0) {
                                $insert_msg_prep_msg = $GLOBALS['link']->prepare("INSERT INTO `messages`(`from_id`, `message`, `image_id`, `group_id`) VALUES (?, ?, ?, ?)");
                                $insert_msg_prep_msg->execute([ $_SESSION['user_id'], $text, $image_id, $groupid]);
                            }
                        }

                        // print_r($GLOBALS['link']->errorInfo());

                        $message_id = $GLOBALS['link']->lastInsertId();
                        $message = $GLOBALS['link']->query("SELECT * FROM `messages` WHERE `id` = {$message_id}")->fetch();
                    }

                    if ($is_user) {
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
                    } elseif ($is_group) {
                        // Send messages to every group member
                        $group_members_stmt = $GLOBALS['link']->query("SELECT * FROM `chat_groups_members` WHERE `group_id` = {$groupid}");
                        
                        while ($member = $group_members_stmt->fetch()) {
                            $member_id = $member['user_id'];

                            // Prevent message from being sent to the sender
                            if ($member_id != $_SESSION['user_id']) {
                                $GLOBALS['link']->query("INSERT INTO `pending_messages`(`to_id`, `message_id`, `group_id`) VALUES ({$member_id}, {$message_id}, {$groupid})");
                                $GLOBALS['link']->query("INSERT INTO `unseen_group_messages`(`user_id`, `group_id`) VALUES ({$member_id}, {$groupid})");
                            }
                        }
                    }
                
                    // Check if message is first between the users
                    if ($is_user) {
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
                }
                
                echo json_encode($resp);
                break;
            case 'close_chatbox' :
                if (isset($_GET['id'])) {
                    $id = $_GET['id'];

                    if (!isset($_GET['group'])) {
                        $GLOBALS['link']->query("DELETE FROM `open_chatboxes` WHERE `user_id` = {$_SESSION['user_id']} AND `to_id` = {$id}");
                    } else {
                        $GLOBALS['link']->query("DELETE FROM `open_chatboxes` WHERE `user_id` = {$_SESSION['user_id']} AND `group_id` = {$id}");
                    }
                }

                break;
            case 'fold_chatbox' :
                if (isset($_GET['id'])) {
                    $id = $_GET['id'];

                    if (!isset($_GET['group'])) {
                        $GLOBALS['link']->query("UPDATE `open_chatboxes` SET `is_folded` = NOT `is_folded` WHERE `user_id` = {$_SESSION['user_id']} AND `to_id` = {$id}");
                    } else {
                        $GLOBALS['link']->query("UPDATE `open_chatboxes` SET `is_folded` = NOT `is_folded` WHERE `user_id` = {$_SESSION['user_id']} AND `group_id` = {$id}");
                    }
                }

                break;
            case 'messages_listen' :
                $pending_messages_stmt = $GLOBALS['link']->query("SELECT * FROM `pending_messages` WHERE `to_id` = {$_SESSION['user_id']} ORDER BY `id`");
                $messages = [];

                while ($pending_message = $pending_messages_stmt->fetch()) {
                    $message = $GLOBALS['link']->query("SELECT * FROM `messages` WHERE `id` = {$pending_message['message_id']} ORDER BY `id`")->fetch();
                    $message_image = false;

                    if ($message['image_id']) {
                        $message_image = get_image_path_by_id($message['image_id']);
                    }

                    if (!$message['group_id']) {
                        // Regular private message
                        array_push($messages, [
                            'userid' => $message['from_id'],
                            'text' => emojify_message(nl2br($message['message'])),
                            'date' => $message['date'],
                            'isSelf' => false,
                            'image' => $message_image,
                        ]);
                    } else {
                        // Group message
                        $sender = get_user_row_by_id($message['from_id']);

                        array_push($messages, [
                            'groupid' => $message['group_id'],
                            'group_userid' => $sender['id'],
                            'fullname' => $sender['fullname'],
                            'text' => emojify_message($message['message']),
                            'date' => $message['date'],
                            'isSelf' => false,
                            'image' => $message_image,
                        ]);
                    }
                }

                // Delete pending messages
                $GLOBALS['link']->query("DELETE FROM `pending_messages` WHERE `to_id` = {$_SESSION['user_id']}");

                echo json_encode($messages);

                break;
            case 'new_group' :
                if (isset($_POST['group_name'], $_POST['group_members'])) {
                    $name = addslashes(htmlentities($_POST['group_name']));
                    $members = $_POST['group_members'];

                    if (!empty($name) && count($members) > 1) {
                        // Create group
                        $GLOBALS['link']->query("INSERT INTO `chat_groups`(`name`, `user_id`) VALUES ('{$name}', {$_SESSION['user_id']})");
                        echo $group_id = $GLOBALS['link']->lastInsertId();


                        
                        $create_message_text = genderize_text('יצר') . ' את הקבוצה';
                        $GLOBALS['link']->query("INSERT INTO `messages`(`from_id`, `message`, `group_id`) VALUES ({$_SESSION['user_id']}, '{$create_message_text}', {$group_id})");
                        $message_id = $GLOBALS['link']->lastInsertId();

                        // Add group members
                        $GLOBALS['link']->query("INSERT INTO `chat_groups_members`(`user_id`, `group_id`) VALUES ({$_SESSION['user_id']}, {$group_id})");

                        foreach ($members as $member) {
                            $GLOBALS['link']->query("INSERT INTO `chat_groups_members`(`user_id`, `group_id`) VALUES ({$member}, {$group_id})");
                        }

                        // Send pending message to members
                        $group_members_stmt = $GLOBALS['link']->query("SELECT * FROM `chat_groups_members` WHERE `group_id` = {$group_id}");
                        
                        while ($member = $group_members_stmt->fetch()) {
                            $member_id = $member['user_id'];

                            // Prevent message from being sent to the sender
                            if ($member_id != $_SESSION['user_id']) {
                                $GLOBALS['link']->query("INSERT INTO `pending_messages`(`to_id`, `message_id`, `group_id`) VALUES ({$member_id}, {$message_id}, {$group_id})");
                            }
                        }
                    }
                }

                break;
            case 'read' :
                if (isset($_GET['userid'])) {
                    $userid = $_GET['userid'];

                    $GLOBALS['link']->query("UPDATE `messages` SET `seen` = 1 WHERE `from_id` = {$userid} AND `to_id` = {$_SESSION['user_id']}");
                }

                if (isset($_GET['groupid'])) {
                    $groupid = $_GET['groupid'];

                    $GLOBALS['link']->query("DELETE FROM `unseen_group_messages` WHERE `user_id` = {$_SESSION['user_id']} AND `group_id` = {$groupid}");
                }
            case 'get_user_chatlist_item' :
                if (isset($_POST['userid'])) {
                    $userid = $_POST['userid'];
                    $user = get_user_row_by_id($userid);

                    $resp = [
                        'fullname' => $user['fullname'],
                        'city' => $user['city'],
                        'unread_messages' => $GLOBALS['link']->query("SELECT * FROM `messages` WHERE `from_id` = {$userid} AND `to_id` = {$_SESSION['user_id']} AND NOT `seen`")->rowCount(),
                        'pp' => get_user_pp_by_id($userid)
                    ];

                    echo json_encode($resp);
                }
                break;
            case 'get_group_chatlist_item' :
                if (isset($_POST['groupid'])) {
                    $groupid = $_POST['groupid'];
                    $group = $GLOBALS['link']->query("SELECT * FROM `chat_groups` WHERE `id` = {$groupid}")->fetch();

                    $resp = [
                        'fullname' => $group['name'],
                        'unread_messages' => $GLOBALS['link']->query("SELECT * FROM `messages` WHERE `group_id` = {$groupid} AND `to_id` = {$_SESSION['user_id']} AND NOT `seen`")->rowCount(),
                        'pp' => $URL . '/img/icons/group-icon.png'
                    ];

                    echo json_encode($resp);
                }
                break;
        }
    }
?>