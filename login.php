<?php
    if (isset($_POST['email'], $_POST['password'])) {
        $email = $_POST['email'];
        $password = $_POST['password'];

        if (do_email_and_pass_match($email, $password)) {
            $_SESSION['user_id'] = get_user_id_by_email($email);

            if (check_csrf()) {
                die('CSRF DETECTED!');
            }
            
            // Set cookie
            setcookie('login_hash', get_login_hash(), time() + (86400 * 30), "/");
        }
    }

    header('Location: ' . $URL);
?>