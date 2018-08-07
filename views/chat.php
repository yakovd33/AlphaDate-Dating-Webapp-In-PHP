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
            <?php $recent_chats = $GLOBALS['link']->query("(SELECT users.* FROM `users` INNER JOIN `messages` ON (`users`.`id` = `messages`.`from_id` OR `users`.`id` = `messages`.`to_id`) AND (`messages`.`from_id` = {$_SESSION['user_id']} OR `messages`.`to_id` = {$_SESSION['user_id']}) AND `users`.`id` <> {$_SESSION['user_id']} GROUP BY `users`.`id`)"); ?>
            <?php while ($chat_user = $recent_chats->fetch()) : ?>
                <div class="item chatbox-trigger" data-userid="<?php echo $chat_user['id']; ?>">
                    <div class="pic"><img src="<?php echo get_user_pp_by_id($chat_user['id']); ?>" alt=""></div>
                    <div class="textual">
                        <div class="fullname">
                            <?php if (is_user_logged($chat_user['id'])) : ?>
                                <span class="logged-indicator"></span>
                            <?php endif; ?>
                            <?php echo $chat_user['fullname']; ?>
                        </div>

                        <div class="distance"><?php echo $chat_user['city']; ?></div>
                    </div>
                </div>
            <?php endwhile; ?>
        </div>
    </div>

    <div id="chat-boxes">
        <?php $open_chatboxes_stmt = $GLOBALS['link']->query("SELECT * FROM `open_chatboxes` WHERE `user_id` = {$_SESSION['user_id']}"); ?>
        <?php while ($chatbox = $open_chatboxes_stmt->fetch()) : ?>
            <?php
                $to_user = get_user_row_by_id($chatbox['to_id']);

                $chat_messages_query = $GLOBALS['link']->query("SELECT * FROM `messages` WHERE (`from_id` = {$_SESSION['user_id']} AND `to_id` = {$to_user['id']}) OR (`from_id` = {$to_user['id']} AND `to_id` = {$_SESSION['user_id']}) ORDER BY `date`");
                $chat_messages = [];

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

                echo $handlebars->render("chatbox", [
                    'userid' => $chatbox['to_id'],
                    'fullname' => $to_user['fullname'],
                    'messages' => $chat_messages,
                    'isFolded' => $chatbox['is_folded'],
                    'isLogged' => is_user_logged($to_user['id'])
                ]);

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