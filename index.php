<?php
    require_once('includes/config.php');
    require_once('includes/functions.php');

    if (!is_logged() && isset($_COOKIE['login_hash']) && $_COOKIE['login_hash'] != 0) {
        $hash = $_COOKIE['login_hash'];
        $_SESSION['user_id'] = $GLOBALS['link']->query("SELECT `user_id` FROM `login_hashes` WHERE `hash` = '{$hash}'")->fetch()['user_id'];
    }

    if (is_logged()) {
        $CUR_USER = get_user_row_by_id($_SESSION['user_id']);
        update_last_seen();
    }
?>

<?php if (!isset($_GET['page'])) : ?>
    <?php if (is_logged()) : ?>
        <?php include 'views/index/logged.php'; ?>
    <?php else : ?>
        <?php include 'views/index/default.php'; ?>
    <?php endif; ?>
<?php else: ?>
        <?php if ($_GET['page'] == 'signin') : ?>
            <?php include 'login.php'; ?>
        <?php elseif ($_GET['page'] == 'join') : ?>
            <?php include 'signup.php'; ?>
        <?php elseif ($_GET['page'] == 'logout') : ?>
            <?php
                logout();
                header("Location: " . $URL);
            ?>
        <?php else : ?>
            <?php include 'views/header.php'; ?>
            
            <?php
                switch ($_GET['page']) {
                    case 'profile' :
                        include 'views/profile.php';
                        break;
                }
            ?>

            <?php include 'views/footer.php'; ?>
        <?php endif; ?>
<?php endif; ?>