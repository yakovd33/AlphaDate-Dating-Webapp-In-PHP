<?php
    require_once(dirname(__DIR__) . '../../facebook-login.php');
?>

<!DOCTYPE html>
<html lang="en" dir="<?php echo $DIRECTION; ?>">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <meta http-equiv="X-UA-Compatible" content="ie=edge">
        <title>Alpha Date</title>
        <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/bootstrap/4.1.1/css/bootstrap.min.css">
        <link rel="stylesheet" href="https://use.fontawesome.com/releases/v5.1.0/css/all.css" integrity="sha384-lKuwvrZot6UHsBSfcMvOkWwlCMgc0TaWr+30HWe3a4ltaBwTZhyTEggF5tJv8tbt" crossorigin="anonymous">
        <link rel="stylesheet" href="<?php echo $URL; ?>/css/main.css">
        <link rel="stylesheet" href="<?php echo $URL; ?>/css/index.css">
        <script src="https://code.jquery.com/jquery-3.3.1.min.js" integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8=" crossorigin="anonymous"></script>
        <!-- Slick JS -->
        <link rel="stylesheet" type="text/css" href="http://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.css"/>
        <script type="text/javascript" src="http://cdn.jsdelivr.net/npm/slick-carousel@1.8.1/slick/slick.min.js"></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/css/bootstrap-datepicker.css">
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/js/bootstrap-datepicker.js"></script>
        <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-datepicker/1.8.0/locales/bootstrap-datepicker.he.min.js"></script>

        <?php if ($DIRECTION == 'ltr') : ?>
            <link rel="stylesheet" href="<?php echo $URL; ?>/css/ltr.css">
            <script>
                window.isRTL = false;
            </script>
        <?php endif; ?>
    </head>
    <body>
        <input type="hidden" id="url" value="<?php echo $URL; ?>">
        <input type="hidden" id="csrf_token" value="<?php echo $_SESSION['csrf_token'] = md5(time() + rand(0, 100)); ?>">

        <script>
            URL = $("#url").val();
        </script>

        <?php include 'views/popups.php'; ?>
        
        <?php if (isset($_GET['tried_too_much'])) : ?>
            <div class="login-msg-wrap">
                <p class="login-msg">
                    נחסמת מההתחברות לאחר 5 נסיונות כושלים. אנא נסה שוב בעוד בחצי שעה.
                </p>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['wrong_login'])) : ?>
            <div class="login-msg-wrap">
                <p class="login-msg">
                    פרטי התחברות שגויים. אנא נסה שוב.
                </p>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['password_changed'])) : ?>
            <div class="login-msg-wrap">
                <p class="login-msg">
                    סיסמא שונתה בהצלחה. אנא נסה/י להתחבר.
                </p>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['password_reset'])) : ?>
            <?php $token = $_GET['password_reset']; ?>
            <?php if ($GLOBALS['link']->query("SELECT * FROM `password_reset_tokens` WHERE `token` = '{$token}' AND NOT `is_used`")->rowCount() > 0) : ?>
                <div id="password-reset-bg"></div>
                <form id="password-reset-form-wrap">
                    <input type="password" id="password-reset-first-input" placeholder="סיסמא">
                    <input type="password" id="password-reset-second-input" placeholder="אימות סיסמא">
                    <input type="hidden" id="password-reset-token" value="<?php echo $_GET['password_reset']; ?>">
                    <input type="submit" value="שחזר סיסמא">

                    <div id="password-change-feedback"></div>
                </form>
            <?php else : ?>
                <div class="login-msg-wrap">
                    <p class="login-msg">
                        קישור לשחזור סיסמא לא קיים/פג תוקף.
                    </p>
                </div>
            <?php endif; ?>
        <?php endif; ?>

        <div id="hero-section" class="section fullscreen-section">
            <div id="hero-nav">
                <div id="hero-nav-logo">
                    <img src="<?php echo $URL; ?>/img/icon-trans.svg" height="55px" alt="">
                </div>

                <div id="hero-nav-links">
                    <a href="#" class="link"></a>
                </div>

                <a href="#" id="nav-hero-login-link">התחברות/הרשמה</a>
            </div>

            <div class="container">
                <div id="hero-signup-card" class="container">
                    <div id="hero-signup-card-content">
                        <div class="hero-signup-card-title">אני</div>
                        <div id="hero-signup-card-gender-chooser">
                            <select class="form-control" name="" id="">
                                <option value="male">זכר</option>
                                <option value="נקבה">נקבה</option>
                            </select>
                        </div>

                        <div class="hero-signup-card-title">בגיל</div>
                        <div id="hero-signup-card-age-chooser">
                            <select name="" id="" class="form-control">
                                <option value="18">18</option>
                            </select>
                        </div>

                        <div class="hero-signup-card-title">מתעניין ב</div>
                        <div id="hero-signup-card-prefrences-chooser">
                            <select class="form-control" name="" id="">
                                <option value="men">גברים</option>
                                <option value="women">נשים</option>
                                <option value="all">הכל</option>
                            </select>
                        </div>

                        <div class="hero-signup-card-title">בגילאים</div>
                        <div id="hero-signup-card-prefrences-chooser">
                            <select class="form-control" name="" id="">
                                <option value="18">18</option>
                            </select>
                        </div>

                        <div class="hero-signup-card-title">עד</div>
                        <div id="hero-signup-card-prefrences-chooser">
                            <select class="form-control" name="" id="">
                                <option value="">35</option>
                            </select>
                        </div>

                        <input type="submit" value="הצטרפות" id="join-btn">
                    </div>

                    <div id="signup-fields">
                        <form action="<?php echo $URL; ?>/join/" id="signup-form-2">
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

                            <!-- <input id="dob-datepicker" name="date_of_birth" type="text" placeholder="תאריך לידה"> -->

                            <label style="display: block; margin-top: 5px; margin-bottom: 3px; text-align: right; font-weight: bold">תאריך לידה</label>
                            <div class="form-row" id="dob-row">
                                <div class="col">
                                    <select name="year" class="register-select" id="">
                                        <option>שנה</option>
                                        <?php for ($i = date("Y") - 100; $i < date("Y") - 18; $i++) : ?>
                                            <option value="<?php echo $i; ?>"><?php echo $i; ?></option>
                                        <?php endfor; ?>
                                    </select>
                                    <!-- <input type="text" name="year" placeholder="שנה"> -->
                                </div>
                                
                                <div class="col">
                                    <select name="month" class="register-select" id="">
                                        <option>חודש</option>
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
                                        <option>יום</option>
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
                                <option value="male">זכר</option>
                                <option value="female">נקבה</option>
                            </select>

                            <div id="signup-form-feedback-2"></div>
                            
                            <input type="submit" value="הרשמו לאלפא דייט">
                        </form>
                    </div>
                </div>
            </div>
        </div>

        <div id="features-section" class="section">
            <h2 class="section-title">למה לבחור בנו מבין כל האתרים?</h2>
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-6 features-section-features-wrap">
                        <div class="feature">
                            <div class="icon"><img src="<?php echo $URL; ?>/img/homepage/features/verified.svg" alt=""></div>
                            <div class="title">חשבונות מאושרים</div>
                            <div class="text">אצלנו באלפא דייט כל החשבונות עוברים סינון לפני שמגיעים אליכם, ככה שתוכלו להיות בטוחים שעשינו כמיטב יכולתנו להביא לכם את הפרופילים האיכותיים ביותר.</div>
                        </div>
                    </div>

                    <div class="col-md-3 col-6 features-section-features-wrap">
                        <div class="feature">
                            <div class="icon"><img src="<?php echo $URL; ?>/img/homepage/features/verified.svg" alt=""></div>
                            <div class="title">חשבונות מאושרים</div>
                            <div class="text">אצלנו באלפא דייט כל החשבונות עוברים סינון לפני שמגיעים אליכם, ככה שתוכלו להיות בטוחים שעשינו כמיטב יכולתנו להביא לכם את הפרופילים האיכותיים ביותר.</div>
                        </div>
                    </div>

                    <div class="col-md-3 col-6 features-section-features-wrap">
                        <div class="feature">
                            <div class="icon"><img src="<?php echo $URL; ?>/img/homepage/features/success.svg" alt=""></div>
                            <div class="title">חשבונות מאושרים</div>
                            <div class="text">אצלנו באלפא דייט כל החשבונות עוברים סינון לפני שמגיעים אליכם, ככה שתוכלו להיות בטוחים שעשינו כמיטב יכולתנו להביא לכם את הפרופילים האיכותיים ביותר.</div>
                        </div>
                    </div>

                    <div class="col-md-3 col-6 features-section-features-wrap">
                        <div class="feature">
                            <div class="icon"><img src="<?php echo $URL; ?>/img/homepage/features/events.svg" alt=""></div>
                            <div class="title">חשבונות מאושרים</div>
                            <div class="text">אצלנו באלפא דייט כל החשבונות עוברים סינון לפני שמגיעים אליכם, ככה שתוכלו להיות בטוחים שעשינו כמיטב יכולתנו להביא לכם את הפרופילים האיכותיים ביותר.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="success-stories-and-stats-section" class="section">
            <div id="stats-section">
                <div class="container">
                    <div class="row">
                        <div class="stat-item col-md-4">
                            <div class="icon"><i class="fas fa-transgender" style="font-size: 48px;"></i></div>
                            <div class="textual">
                                <div class="dets">באתר קיימים 4576 פרופילים פעילים</div>
                            </div>
                        </div>

                        <div class="stat-item col-md-4">
                            <div class="icon"><i class="fas fa-transgender" style="font-size: 48px;"></i></div>
                            <div class="textual">
                                <div class="dets">באתר קיימים 4576 פרופילים פעילים</div>
                            </div>
                        </div>

                        <div class="stat-item col-md-4">
                            <div class="icon"><i class="fas fa-transgender" style="font-size: 48px;"></i></div>
                            <div class="textual">
                                <div class="dets">באתר קיימים 4576 פרופילים פעילים</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="success-stories-section">
                <h2 class="section-title">סיפורי הצלחה</h2>

                <div id="success-stories-slider">
                    <div class="success-story-item-wrap">
                        <div class="success-story-item">
                            <div class="pic"><img src="https://i.imgur.com/QP3b8gb.png" alt=""></div>
                            <div class="textual">
                                <div class="names">הרצל ודניאלה</div>
                                <div class="text">” אהובי הנצחי. אין תחושה יותר טובה מלהתעורר אליה כל בוקר. ”</div>
                            </div>
                        </div>
                    </div>

                    <div class="success-story-item-wrap">
                        <div class="success-story-item">
                            <div class="pic"><img src="https://i.imgur.com/QP3b8gb.png" alt=""></div>
                            <div class="textual">
                                <div class="names">הרצל ודניאלה</div>
                                <div class="text">” אהובי הנצחי. אין תחושה יותר טובה מלהתעורר אליה כל בוקר. ”</div>
                            </div>
                        </div>
                    </div>

                    <div class="success-story-item-wrap">
                        <div class="success-story-item">
                            <div class="pic"><img src="https://i.imgur.com/QP3b8gb.png" alt=""></div>
                            <div class="textual">
                                <div class="names">הרצל ודניאלה</div>
                                <div class="text">” אהובי הנצחי. אין תחושה יותר טובה מלהתעורר אליה כל בוקר. ”</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="join-us-section" class="section">
            <a href="#" id="join-us-section-join-btn">הצטרפו עכשיו</a>
        </div>

        <!-- <fb:login-button></fb:login-button> -->
        
        <script>
            // This is called with the results from from FB.getLoginStatus().
            function statusChangeCallback(response) {
                console.log(response);
                if (response.status === 'connected') {
                    // Logged into your app and Facebook.
                    // console.log('logged');
                    // testAPI();
                    console.log(response);
                } else {
                    // The person is not logged into your app or we are unable to tell.
                    console.log('not logged');
                }
            }

            function checkLoginState() {
                FB.getLoginStatus(function(response) {
                statusChangeCallback(response);
                });
            }

            window.fbAsyncInit = function() {
                FB.init({
                appId      : '1413642492113699',
                cookie     : true,
                xfbml      : true,
                version    : 'v2.8'
                });

                FB.getLoginStatus(function(response) {
                    statusChangeCallback(response);
                });

            };

            // Load the SDK asynchronously
            (function(d, s, id) {
                var js, fjs = d.getElementsByTagName(s)[0];
                if (d.getElementById(id)) return;
                js = d.createElement(s); js.id = id;
                js.src = "https://connect.facebook.net/en_US/sdk.js";
                fjs.parentNode.insertBefore(js, fjs);
            }(document, 'script', 'facebook-jssdk'));

            // Here we run a very simple test of the Graph API after login is
            // successful.  See statusChangeCallback() for when this call is made.
            function testAPI() {
                console.log('Welcome!  Fetching your information.... ');
                FB.api('/me', function(response) {
                    console.log('Successful login for: ' + response.name);
                });
            }

            // $("#login-with-facebook-btn").click(function () {
            //     FB.login();
            // });

            function fblogout(response) {
                if (!response.authResponse) {
                    return;
                }
                
                FB.logout(response.authResponse);
            }
        </script>
        <script src="<?php echo $URL; ?>/js/index.js"></script>
    </body>
</html>