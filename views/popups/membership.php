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
                    <a href="<?php echo $login_url; ?>"><div id="login-with-facebook-btn"><?php echo $translate['login_with_facebook']; ?></div></a>
                </div>

                <input type="email" name="email" placeholder="<?php echo $translate['email']; ?>">
                <input type="password" name="password" placeholder="<?php echo $translate['password']; ?>">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                <a href="#" id="forgot-pass-btn"><?php echo $translate['forgot_password']; ?></a>

                <input type="submit" value="<?php echo $translate['login_to_alphadate']; ?>">
            </form>

            <form action="" id="forgot-poassword-form">
                <input type="email" placeholder="<?php echo $translate['email']; ?>" id="password-reset-email-input">
                <input type="submit" value="<?php echo $translate['reset_password']; ?>">
                <a href="" id="close-forgot-pass-form"><?php echo $translate['cancel']; ?></a>
            </form>
        </div>

        <div class="tab" data-tab="signup">
            <form action="<?php echo $URL; ?>/join/" id="signup-form">
                <input type="hidden" name="csrf_token" value="<?php echo $_SESSION['csrf_token']; ?>">
                
                <div id="facebook-signup-btn-wrap">
                    <div id="signup-with-facebook-btn"><?php echo $translate['join_with_facebook']; ?></div>
                </div>

                <div class="form-row">
                    <div class="col">
                        <input type="email" name="email" placeholder="<?php echo $translate['email']; ?>">
                    </div>
                    
                    <div class="col">
                        <input type="password" name="password" placeholder="<?php echo $translate['password']; ?>">
                    </div>
                </div>

                <input type="text" name="fullname" placeholder="<?php echo $translate['fullname']; ?>">

                <!-- <input id="dob-datepicker" name="date_of_birth" type="text" placeholder="תאריך לידה"> -->

                <label style="display: block; margin-top: 5px; margin-bottom: 3px; text-align: right; font-weight: bold">תאריך לידה</label>
                <div class="form-row" id="dob-row">
                    <div class="col">
                        <select name="year" class="register-select" id="">
                            <option><?php echo $translate['year']; ?></option>
                            <?php for ($i = date("Y") - 100; $i < date("Y") - 18; $i++) : ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                        <!-- <input type="text" name="year" placeholder="שנה"> -->
                    </div>
                    
                    <div class="col">
                        <select name="month" class="register-select" id="">
                            <option><?php echo $translate['month']; ?></option>
                            <option value="1">ינואר</option>
                            <option value="2">פברואר</option>
                            <option value="3">מרץ</option>
                            <option value="4">אפריל</option>
                            <option value="5">מאי</option>
                            <option value="6">יוני</option>
                            <option value="7">יולי</option>
                            <option value="8">אוגוסט</option>
                            <option value="9">ספטמבר</option>
                            <option value="10">אוקטובר</option>
                            <option value="11">נובמבר</option>
                            <option value="12">דצמבר</option>
                        </select>
                        <!-- <input type="text" name="day" placeholder="יום"> -->
                    </div>

                    <div class="col">
                        <select name="day" class="register-select" id="">
                            <option><?php echo $translate['day']; ?></option>
                            <?php for ($i = 0; $i < 31; $i++) : ?>
                                <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                            <?php endfor; ?>
                        </select>
                        <!-- <input type="text" name="month" placeholder="חודש"> -->
                    </div>
                </div>

                <!-- <script>
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
                </script> -->

                <select name="gender" id="" class="form-control" style="margin-top: 15px;">
                    <option value="male"><?php echo $translate['male']; ?></option>
                    <option value="female"><?php echo $translate['female']; ?></option>
                </select>

                <div id="signup-form-feedback"></div>
                
                <input type="submit" value="<?php echo $translate['join_alphadate']; ?>">
            </form>
        </div>
    </div>
</div>