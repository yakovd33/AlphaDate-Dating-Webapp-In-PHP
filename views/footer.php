        <?php
            if ((isset($_GET['page']) && $_GET['page'] != 'conversation') || !isset($_GET['page'])) {
                include 'chat.php';
            }
        ?>

        <?php if (isset($_GET['page']) && $_GET['page'] != 'profile' || !isset($_GET['page'])) : ?>
            </div>
        <?php endif; ?>

        <script id="post-comment-template" type="text/x-handlebars-template">
            <?php include 'templates/post_comment.hbs'; ?>
        </script>

        <script src="<?php echo $URL; ?>/js/main.js"></script>

        <script src="<?php echo $URL; ?>/js/config.js"></script>
        <script src="<?php echo $URL; ?>/js/util.js"></script>
        <script src="<?php echo $URL; ?>/js/jquery.emojiarea.js"></script>
        <script src="<?php echo $URL; ?>/js/emoji-picker.js"></script>

        <script>
            $(function() {
                window.emojiPicker = new EmojiPicker({
                emojiable_selector: '[data-emojiable=true]',
                assetsPath: URL + '/img/emojis',
                popupButtonClasses: 'fas fa-smile-o'
                });
                window.emojiPicker.discover();
            });
        </script>
    </body>
</html>