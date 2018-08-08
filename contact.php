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

    if (isset($_POST['message'], $_POST['contact'], $_POST['name'])) {
        $message = $_POST['message'];
        $contact = $_POST['contact'];
        $name = $_POST['name'];

        $GLOBALS['link']->query("INSERT INTO `contact_form_submitions`(`contact`, `name`, `message`) VALUES ('{$contact}', '{$name}', '{$message}')");

        header("Location: " . $URL);
    }
?>

<?php include 'views/header.php'; ?>
    <link rel="stylesheet" href="<?php echo $URL; ?>/css/contact.css">

    <h1 id="contact-form-title">צור קשר</h1>
    <form class="cf" method="POST">
        <div class="halfs-group">
            <div class="half left cf">
                <input type="text" name="name" id="input-name" placeholder="שם">
            </div>
            
            <div class="half right cf">
                <input type="text" name="contact" id="input-name" placeholder="אימייל/מספר טלפון">
            </div>
        </div>

        <div class="cf">
            <textarea name="message" type="text" id="input-message" placeholder="הודעה"></textarea>
        </div>  
        <input type="submit" value="שלח" id="input-submit">
    </form>
<?php include 'views/footer.php'; ?>