<?php
    require_once(dirname(__DIR__) . '../../facebook-login.php');
?>

<!DOCTYPE html>
<html lang="en" dir="rtl">
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
    </head>
    <body>
        <?php include 'views/popups.php'; ?>
        
        <div id="hero-section" class="section fullscreen-section">
            <div id="hero-nav">
                <div id="hero-nav-logo">
                    <img src="img/icon-trans.svg" height="55px" alt="">
                </div>

                <div id="hero-nav-links">
                    <a href="#" class="link"></a>
                </div>

                <a href="#" id="nav-hero-login-link">התחברות/הרשמה</a>
            </div>

            <div class="container">
                <div id="hero-signup-card" class="container">
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

                    <input type="submit" value="חיפוש">
                </div>
            </div>
        </div>

        <div id="features-section" class="section fullscreen-section">
            <h2 class="section-title">למה לבחור בנו מבין כל האתרים?</h2>
            <div class="container">
                <div class="row">
                    <div class="col-md-3 col-6 features-section-features-wrap">
                        <div class="feature">
                            <div class="icon"><img src="img/homepage/features/verified.svg" alt=""></div>
                            <div class="title">חשבונות מאושרים</div>
                            <div class="text">אצלנו באלפא דייט כל החשבונות עוברים סינון לפני שמגיעים אליכם, ככה שתוכלו להיות בטוחים שעשינו כמיטב יכולתנו להביא לכם את הפרופילים האיכותיים ביותר.</div>
                        </div>
                    </div>

                    <div class="col-md-3 col-6 features-section-features-wrap">
                        <div class="feature">
                            <div class="icon"><img src="img/homepage/features/verified.svg" alt=""></div>
                            <div class="title">חשבונות מאושרים</div>
                            <div class="text">אצלנו באלפא דייט כל החשבונות עוברים סינון לפני שמגיעים אליכם, ככה שתוכלו להיות בטוחים שעשינו כמיטב יכולתנו להביא לכם את הפרופילים האיכותיים ביותר.</div>
                        </div>
                    </div>

                    <div class="col-md-3 col-6 features-section-features-wrap">
                        <div class="feature">
                            <div class="icon"><img src="img/homepage/features/success.svg" alt=""></div>
                            <div class="title">חשבונות מאושרים</div>
                            <div class="text">אצלנו באלפא דייט כל החשבונות עוברים סינון לפני שמגיעים אליכם, ככה שתוכלו להיות בטוחים שעשינו כמיטב יכולתנו להביא לכם את הפרופילים האיכותיים ביותר.</div>
                        </div>
                    </div>

                    <div class="col-md-3 col-6 features-section-features-wrap">
                        <div class="feature">
                            <div class="icon"><img src="img/homepage/features/events.svg" alt=""></div>
                            <div class="title">חשבונות מאושרים</div>
                            <div class="text">אצלנו באלפא דייט כל החשבונות עוברים סינון לפני שמגיעים אליכם, ככה שתוכלו להיות בטוחים שעשינו כמיטב יכולתנו להביא לכם את הפרופילים האיכותיים ביותר.</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div id="success-stories-and-stats-section" class="section fullscreen-section">
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
                                <div class="text">” הסקס הכי טוב שהיה לי. אין תחושה יותר טובה מלהתעורר אליה כל בוקר. ”</div>
                            </div>
                        </div>
                    </div>

                    <div class="success-story-item-wrap">
                        <div class="success-story-item">
                            <div class="pic"><img src="https://i.imgur.com/QP3b8gb.png" alt=""></div>
                            <div class="textual">
                                <div class="names">הרצל ודניאלה</div>
                                <div class="text">” הסקס הכי טוב שהיה לי. אין תחושה יותר טובה מלהתעורר אליה כל בוקר. ”</div>
                            </div>
                        </div>
                    </div>

                    <div class="success-story-item-wrap">
                        <div class="success-story-item">
                            <div class="pic"><img src="https://i.imgur.com/QP3b8gb.png" alt=""></div>
                            <div class="textual">
                                <div class="names">הרצל ודניאלה</div>
                                <div class="text">” הסקס הכי טוב שהיה לי. אין תחושה יותר טובה מלהתעורר אליה כל בוקר. ”</div>
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