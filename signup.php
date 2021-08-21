<?php
    if (isset($_POST['fullname'], $_POST['email'], $_POST['password'], $_POST['gender'], $_POST['year'], $_POST['month'], $_POST['day'])) {
        $fullname = $_POST['fullname'];
        $email = $_POST['email'];
        $password = $_POST['password'];
        $gender = $_POST['gender'];
        $day = $_POST['day'];
        $month = $_POST['month'];
        $year = $_POST['year'];

        if ($month < 10) {
            $month = '0' . $month;
        }

        if ($day < 10) {
            $day = '0' . $day;
        }

        $date_of_birth = "$year-$month-$day";

        if (check_csrf()) {
            die('CSRF DETECTED!');
        }

        if (!empty($fullname) && !empty($email) && !empty($password) && !empty($date_of_birth) && !empty($gender)) {
            if (!email_exists($email)) {
                if ($gender != 'male' && $gender != 'female') {
                    $gender = 'male';
                }

                $profile_hash = substr(md5(time() + rand(0, 10000)), 0, 5);

                if (preg_match("/^[0-9]{4}-(0[1-9]|1[0-2])-(0[1-9]|[1-2][0-9]|3[0-1])$/", $date_of_birth)) {
                    if ($year <= date("Y") - 19) {
                        $insert_prep_stmt = $GLOBALS['link']->prepare("INSERT INTO `users`(`fullname`, `email`, `password_hashed`, `date_of_birth`, `gender`, `profile_hash`) VALUES (?, ?, ?, ?, ?, ?)");
                        $insert_prep_stmt->execute([$fullname, $email, passsword_hash($password), $date_of_birth, $gender, $profile_hash]);
                        $_SESSION['user_id'] = $GLOBALS['link']->lastInsertId();
                        echo 'success';
                        // echo 'נרשמת. נסה להתחבר עכשיו';
                    } else {
                        echo 'אתה צעיר מדי בכדי להירשם';
                    }
                } else {
                    echo 'פורמט תאריך לא תקין.';
                }
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