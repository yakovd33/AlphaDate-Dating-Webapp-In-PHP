<div class="container" id="chat-wrap">
    <div id="floating-chat">
        <div id="floating-chat-toggler">
            <span>
                צ'אט
                <!-- <span id="chat-num-connected">(<?php //echo get_num_connected_followed(); ?>)</span> -->
            </span>

            <div id="chat-toggler-options">
                <div class="chat-toggler-option">
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

                <div class="item chatbox-trigger" <?php if ($is_user) { echo 'data-userid="' . $chat_user['id'] . '"'; } ?> <?php if ($is_group) { echo 'data-groupid="' . $group['id'] . '"'; } ?>>
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
                </div>
            <?php endforeach; ?>
        </div>
    </div>

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
</div>

<script id="chatbox-template" type="text/x-handlebars-template">
    <?php include 'templates/chatbox.hbs'; ?>
</script>

<script id="chat-message-template" type="text/x-handlebars-template">
    <?php include 'templates/chat_message.hbs'; ?>
</script>

<script src="<?php echo $URL; ?>/js/chat.js"></script>