<link rel="stylesheet" href="<?php echo $URL; ?>/css/chat.css">
<link rel="stylesheet" href="<?php echo $URL; ?>/css/chat_full.css">

<script id="chatbox-template" type="text/x-handlebars-template">
    <?php include 'templates/chatbox.hbs'; ?>
</script>

<script id="chat-message-template" type="text/x-handlebars-template">
    <?php include 'templates/chat_message.hbs'; ?>
</script>

<script>
    is_fullscreen_chat = true;
</script>

<div class="col-md-3" id="right-sidebar-wrap">
    <?php include 'views/sidebars/right-sidebar.php'; ?>
</div>

<?php
    $no_chatboxes = true;
    include 'chat.php';
?>

<script>
    fullscreen_chat_id = <?php echo $_GET['id']; ?>
</script>

<div id="chat-boxes"></div>

<?php
    if (isset($_GET['id'])) {
        $id = $_GET['id'];

        if (!isset($_GET['group'])) {
            echo '
                <script>
                    open_chatbox(' . $id . ');
                </script>
            ';
        } else {
            echo '
                <script>
                    open_group_chatbox(' . $id . ');
                </script>
            ';
        }
    }
?>

<script>
    setTimeout(chatbox_options, 1000);
</script>