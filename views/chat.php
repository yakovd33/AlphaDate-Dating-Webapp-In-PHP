<div class="container" id="chat-wrap">
    <div id="floating-chat">
        <div id="floating-chat-toggler" data-num="<?php echo get_num_unread_messages(); ?>">
            <span id="chat-toggler-text">
                צ'אט
                <!-- <span id="chat-num-connected">(<?php //echo get_num_connected_followed(); ?>)</span> -->
            </span>

            <div id="chat-toggler-options">
                <div class="chat-toggler-option" id="chat-new-group">
                    <i class="far fa-edit"></i>
                </div>

                <div class="chat-toggler-option">
                    <i class="fa fa-cog"></i>
                </div>
            </div>
        </div>

        <div id="floating-chat-connected-users">
            <?php
                $recent_chats = $GLOBALS['link']->query("(SELECT users.* FROM `users` INNER JOIN `messages` ON (`users`.`id` = `messages`.`from_id` OR `users`.`id` = `messages`.`to_id`) AND (`messages`.`from_id` = {$_SESSION['user_id']} OR `messages`.`to_id` = {$_SESSION['user_id']}) AND `users`.`id` <> {$_SESSION['user_id']} GROUP BY `users`.`id`)");
                $recent_chat_groups = $GLOBALS['link']->query("SELECT `group_id` FROM `messages` WHERE 1 " . get_user_chatgroup_list_by_col('group_id') . " GROUP BY `group_id`");

                $total_chats = [];
                while ($chat = $recent_chats->fetch()) {
                    // array_push($total_chats, $chat);
                    $user_id = $chat['id'];
                    $user_most_recent_message = $GLOBALS['link']->query("SELECT * FROM `messages` WHERE (`from_id` = {$user_id} AND `to_id` = {$_SESSION['user_id']}) OR (`to_id` = {$user_id} AND `from_id` = {$_SESSION['user_id']}) ORDER BY `date` DESC LIMIT 1")->fetch();
                    array_push($total_chats, $user_most_recent_message);
                }

                while ($chat = $recent_chat_groups->fetch()) {
                    //array_push($total_chats, $chat);
                    $group_id = $chat['group_id'];
                    $chat_group_most_recent_message = $GLOBALS['link']->query("SELECT *, 1 AS `is_group` FROM `messages` WHERE `group_id` = {$group_id} ORDER BY `date` DESC LIMIT 1")->fetch();
                    array_push($total_chats, $chat_group_most_recent_message);
                }

                usort($total_chats, 'sort_by_date');
            ?>
            
            <?php foreach ($total_chats as $chat) : ?>
                <?php
                    $is_user = isset($chat['is_group']) ? false : true;
                    $is_group = isset($chat['is_group']) ? true : false;

                    if ($is_group) {
                        $group_id = $chat['group_id'];
                        $group = $GLOBALS['link']->query("SELECT * FROM `chat_groups` WHERE `id` = {$group_id}")->fetch();
                    } elseif ($is_user) {
                        $user_id = $chat['from_id'] != $_SESSION['user_id'] ? $chat['from_id'] : $chat['to_id'];
                        $chat_user = get_user_row_by_id($user_id);
                    }
                ?>

                <div class="item chatbox-trigger" data-userid="<?php if ($is_user) { echo $chat_user['id']; } ?>" data-groupid="<?php if ($is_group) { echo $group['id']; } ?>">
                    <?php if ($is_user) : ?>
                        <div class="pic"><img src="<?php echo get_user_pp_by_id($chat_user['id']); ?>" alt=""></div>
                    <?php elseif ($is_group) : ?>
                        <div class="pic"><img src="<?php echo $URL; ?>/img/icons/group-icon.png" alt=""></div>
                    <?php endif; ?>
                    
                    <div class="textual">
                        <div class="fullname">
                            <?php if ($is_user) : ?>
                                <?php if (is_user_logged($chat_user['id'])) : ?>
                                    <span class="logged-indicator"></span>
                                <?php endif; ?>
                                <?php echo $chat_user['fullname']; ?>
                            <?php elseif ($is_group) : ?>
                                <?php echo $group['name']; ?>
                            <?php endif; ?>
                        </div>

                        <?php if ($is_user) : ?>
                            <div class="distance"><?php echo $chat_user['city']; ?></div>
                        <?php endif; ?>
                    </div>

                    <div class="chat-list-unread-msgs-marker">
                        <?php if ($is_group) : ?>
                            <?php echo $GLOBALS['link']->query("SELECT * FROM `unseen_group_messages` WHERE `group_id` = {$group_id} AND `user_id` = {$_SESSION['user_id']}")->rowCount(); ?>
                        <?php else : ?>
                            <?php echo $GLOBALS['link']->query("SELECT * FROM `messages` WHERE `from_id` = {$user_id} AND `to_id` = {$_SESSION['user_id']} AND NOT `seen`")->rowCount(); ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>

            <?php if (count($total_chats) == 0) : ?>
                <div id="no-chats-msg">אין שיחות להצגה.</div>
            <?php endif; ?>
        </div>
    </div>

    <?php if (!isset($no_chatboxes)) : ?>
        <div id="chat-boxes">
            <?php $open_chatboxes_stmt = $GLOBALS['link']->query("SELECT * FROM `open_chatboxes` WHERE `user_id` = {$_SESSION['user_id']}"); ?>
            <?php while ($chatbox = $open_chatboxes_stmt->fetch()) : ?>
                <?php
                    if ($chatbox['to_id']) {
                        $to_user = get_user_row_by_id($chatbox['to_id']);
                    }

                    $group_id = $chatbox['group_id'];
                    
                    if ($chatbox['to_id']) {
                        $chat_messages_query = $GLOBALS['link']->query("SELECT * FROM `messages` WHERE (`from_id` = {$_SESSION['user_id']} AND `to_id` = {$to_user['id']}) OR (`from_id` = {$to_user['id']} AND `to_id` = {$_SESSION['user_id']}) ORDER BY `date`");
                    } elseif ($chatbox['group_id']) {
                        $chat_messages_query = $GLOBALS['link']->query("SELECT * FROM `messages` WHERE `group_id` = {$group_id} ORDER BY `date`");
                    }

                    $chat_messages = [];

                    while ($message = $chat_messages_query->fetch()) {

                        $message_image = false;

                        if ($message['image_id']) {
                            $message_image = get_image_path_by_id($message['image_id']);
                        }

                        $sender = get_user_row_by_id($message['from_id']);
                        $message_final = [
                            'userid' => $message['from_id'],
                            'text' => $message['message'],
                            'date' => $message['date'],
                            'isSelf' => ($message['from_id'] == $_SESSION['user_id']),
                            'image' => $message_image,
                        ];

                        if ($message['group_id']) {
                            $message_final['fullname'] = $sender['fullname'];
                        }
                        
                        array_push($chat_messages, $message_final);
                    }

                    if ($chatbox['to_id']) {
                        echo $handlebars->render("chatbox", [
                            'userid' => $chatbox['to_id'],
                            'fullname' => $to_user['fullname'],
                            'messages' => $chat_messages,
                            'isFolded' => $chatbox['is_folded'],
                            'isLogged' => is_user_logged($to_user['id'])
                        ]);
                    } elseif ($chatbox['group_id']) {
                        $group = $GLOBALS['link']->query("SELECT * FROM `chat_groups` WHERE `id` = {$group_id}")->fetch();

                        echo $handlebars->render("chatbox", [
                            'groupid' => $chatbox['group_id'],
                            'group_name' => $group['name'],
                            'messages' => $chat_messages,
                            'isFolded' => $chatbox['is_folded'],
                            'isLogged' => false
                        ]);
                    }

                    // Read messages
                    $GLOBALS['link']->query("DELETE FROM `pending_messages` WHERE `to_id` = {$_SESSION['user_id']}");
                ?>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>
</div>

<form id="new-group-popup">
    <input type="text" class="cute-input" name="group_name" id="new-group-name" placeholder="שם הקבוצה">

    <label class="new-group-popup-label">בחירת חברים</label>

    <div id="new-group-popup-members-select-list">
        <?php foreach ($total_chats as $chat) : ?>
            <?php if (!$chat['group_id']) : ?>
                <?php $user_id = $chat['from_id'] == $_SESSION['user_id'] ? $chat['to_id'] : $chat['from_id']; ?>
                <?php $user = get_user_row_by_id($user_id); ?>

                <label class="member" for="new-group-member-<?php echo $user_id; ?>">
                    <div class="pp"><img src="<?php echo get_user_pp_by_id($user_id); ?>" alt=""></div>
                    <div class="dets">
                        <div class="fullname"><?php echo $user['fullname']; ?></div>
                    </div>

                    <input type="checkbox" id="new-group-member-<?php echo $user_id; ?>" name="group_members[]" value="<?php echo $user_id; ?>">
                </label>
            <?php endif; ?>
        <?php endforeach; ?>
    </div>

    <button class="cute-btn" style="float: left" type="submit">צור קבוצה</button>
    <div class="clearfix"></div>
</form>

<script id="chatbox-template" type="text/x-handlebars-template">
    <?php include 'templates/chatbox.hbs'; ?>
</script>

<script id="chat-message-template" type="text/x-handlebars-template">
    <?php include 'templates/chat_message.hbs'; ?>
</script>

<script>
    is_fullscreen_chat = false;
</script>

<script src="<?php echo $URL; ?>/js/chat.js"></script>

<script id="connected-users-list-template" type="text/x-handlebars-template">
    <div class="item chatbox-trigger" data-userid="{{ userid }}" data-groupid="{{ groupid }}">
        <div class="pic"><img src="{{ pp }}"></div>
                                    
        <div class="textual">
            <div class="fullname">{{ fullname }}</div>
            <div class="distance">{{ city }}</div>
        </div>

        <div class="chat-list-unread-msgs-marker">{{ unread_messages }}</div>
    </div>
</script>