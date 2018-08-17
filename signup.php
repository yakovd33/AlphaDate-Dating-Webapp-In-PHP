<?php
    if (isset($_POST['fullname'], $_POST['email'], $_POST['password'], $_POST['date_of_birth'], $_POST['gender'])) {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $date_of_birth = $_POST['date_of_birth'];
        $gender = $_POST['gender'];

        if (check_csrf()) {
            die('CSRF DETECTED!');
        }

        if (!empty($fullname) && !empty($email) && !empty($password) && !empty($date_of_birth) && !empty($gender)) {
            if (!email_exists($email)) {
                if ($gender != 'male' && $gender != 'female') {
                    $gender = 'male';
                }

                $insert_prep_stmt = $GLOBALS['link']->prepare("INSERT INTO `users`(`fullname`, `email`, `password_hashed`, `date_of_birth`, `gender`) VALUES (?, ?, ?, ?, ?)");
                $insert_prep_stmt->execute([$fullname, $email, passsword_hash($password), $date_of_birth, $gender]);
                echo 'נרשמת. נסה להתחבר עכשיו';
            } else {
                echo 'אימייל קיים במערכת.';
            }
        } else {
            echo 'שדות חסרים.';
        }
    } else {
        echo 'שדות חסרים';
    }
?>