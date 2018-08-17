<input type="hidden" id="csrf_token" value="<?php echo $_SESSION['csrf_token'] = md5(time() + rand(0, 100)); ?>">

<div class="popup" id="membreship-popup" style="display: block;">
    <div id="membreship-popup-tabs-togglers">
        <div class="tab active" data-tab="login">יש לכם חשבון? התחברו</div>
        <div class="tab" data-tab="signup">לקוחות חדשים? הירשמו</div>
    </div>

    <div id="membreship-popup-tabs">
        <div class="tab active" data-tab="login">
            <form action="<?php echo $URL; ?>/signin/" method="post" id="login-form">
                <div id="facebook-login-btn-wrap">
                    <a href="<?php echo $login_url; ?>"><div id="login-with-facebook-btn">התחבר באמצעות פייסבוק</div></a>
                </div>

                <input type="email" name="email" placeholder="אימייל">
                <input type="password" name="password" placeholder="סיסמא">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <a href="#" id="forgot-pass-btn">שכחתי סיסמא</a>

                <input type="submit" value="התחברו לאלפא דייט">
            </form>

            <form action="" id="forgot-poassword-form">
                <input type="email" placeholder="אימייל">
                <input type="submit" value="שחזור סיסמא">
                <a href="" id="close-forgot-pass-form">ביטול</a>
            </form>
        </div>

        <div class="tab" data-tab="signup">
            <form action="<?php echo $URL; ?>/join/" id="signup-form">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div id="facebook-signup-btn-wrap">
                    <div id="signup-with-facebook-btn">הרשם באמצעות פייסבוק</div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <input type="email" name="email" placeholder="אימייל">
                    </div>
                    
                    <div class="col">
                        <input type="password" name="password" placeholder="סיסמא">
                    </div>
                </div>

                <input type="text" name="fullname" placeholder="שם מלא">

                <input id="dob-datepicker" name="date_of_birth" type="text" placeholder="תאריך לידה">
                <script>
                    $('#dob-datepicker').datepicker({
                        language: "he",
                        icons: {
                            next: '<i class="fa fa-chevron-circle-right"></i>',
                            previous: 'fa fa-chevron-circle-left'
                        },
                        format: "yyyy-mm-dd"
                    });

                    $('#dob-datepicker').datepicker().on('changeDate', function () {
                        $('body').append('<style>.datepicker table tr td.active[data-date="' + $(".day.active").data('date') + '"]:after{content: "' + $(".day.active").text() + '"}</style>');  
                    });
                </script>

                <select name="gender" id="" class="form-control" style="margin-top: 15px;">
                    <option value="male">זכר</option>
                    <option value="female">נקבה</option>
                </select>

                <div id="signup-form-feedback"></div>
                
                <input type="submit" value="הרשמו לאלפא דייט">
            </form>
        </div>
    </div>
</div>