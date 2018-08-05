        <?php include 'chat.php'; ?>

        <?php if (isset($_GET['page']) && $_GET['page'] != 'profile' || !isset($_GET['page'])) : ?>
            </div>
        <?php endif; ?>

        <script id="post-comment-template" type="text/x-handlebars-template">
            <?php include 'templates/post_comment.hbs'; ?>
        </script>

        <script src="<?php echo $URL; ?>/js/main.js"></script>
    </body>
</html>